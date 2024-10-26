<?php

namespace app\managers;

use app\common\DomainException;
use app\enums\DomainErrors;
use app\models\Asset;
use app\models\Transaction;
use app\models\Wallet;
use yii\db\Exception;

class WalletManager
{
    const TRANSACTION_BLOCK_SIZE = 11;

    private Wallet $wallet;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    /**
     * @param Asset $asset
     * @return float
     * @throws DomainException
     */
    public function getBalance(Asset $asset): float
    {
        $transactions = Transaction::find()
            ->where([
                'wallet_id' => $this->wallet->id,
                'asset_id' => $asset->id,
            ])
            ->orderBy(['created_at' => SORT_DESC])
            ->limit(self::TRANSACTION_BLOCK_SIZE)
            ->all();

        /**
         * @var Transaction $firstBlockTransaction
         */
        $firstBlockTransaction = count($transactions) === self::TRANSACTION_BLOCK_SIZE
            ? array_pop($transactions) : null;

        $blockBalance = array_reduce($transactions, function ($carry, $item) {
            /**
             * @var Transaction $item
             */
            return $item->operation === Transaction::OPERATION_DEBIT
                ? $carry + $item->amount : $carry - $item->amount;
        });

        /**
         * @var Transaction $lastBlockTransaction
         */
        $lastBlockTransaction = array_shift($transactions);

        $calculatedBalance = $firstBlockTransaction
            ? $firstBlockTransaction->balance + $blockBalance
            : $blockBalance;

        if ($calculatedBalance !== $lastBlockTransaction->balance) {
            throw new DomainException(DomainErrors::INCORRECT_BALANCE);
        }

        return $calculatedBalance;
    }

    /**
     * @param Asset $asset
     * @param float $amount
     * @return bool
     * @throws DomainException
     */
    public function checkBalance(Asset $asset, float $amount): bool
    {
        return $this->getBalance($asset) >= $amount;
    }

    /**
     * @param Asset $asset
     * @param float $amount
     * @return void
     * @throws DomainException
     * @throws Exception
     */
    public function debit(Asset $asset, float $amount): void
    {
        Transaction::add([
            'wallet_id' => $this->wallet->id,
            'asset_id' => $asset->id,
            'amount' => $amount,
            'operation' => Transaction::OPERATION_DEBIT,
            'balance' => $this->getBalance($asset) + $amount,
        ]);
    }

    /**
     * @param Asset $asset
     * @param float $amount
     * @return void
     * @throws DomainException
     * @throws Exception
     */
    public function credit(Asset $asset, float $amount): void
    {
        Transaction::add([
            'wallet_id' => $this->wallet->id,
            'asset_id' => $asset->id,
            'amount' => $amount,
            'operation' => Transaction::OPERATION_DEBIT,
            'balance' => $this->getBalance($asset) - $amount,
        ]);
    }
}