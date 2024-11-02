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

    const COMPARISON_ACCURACY = 12;

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
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
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
                ? (float) bcadd($carry, $item->amount, self::COMPARISON_ACCURACY)
                : (float) bcsub($carry, $item->amount, self::COMPARISON_ACCURACY);
        });

        /**
         * @var Transaction $lastBlockTransaction
         */
        $lastBlockTransaction = count($transactions) > 0
            ? array_shift($transactions) : null;

        $calculatedBalance = $firstBlockTransaction
            ? (float) bcadd($firstBlockTransaction->balance, $blockBalance, self::COMPARISON_ACCURACY)
            : $blockBalance;

        if ($lastBlockTransaction && bccomp($calculatedBalance, $lastBlockTransaction->balance, self::COMPARISON_ACCURACY) !== 0) {
            throw new DomainException(DomainErrors::INCORRECT_BALANCE);
        }

        return $calculatedBalance ?: 0;
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
     * @param string $operation
     * @return void
     * @throws DomainException
     * @throws Exception
     */
    public function add(Asset $asset, float $amount, string $operation)
    {
        Transaction::add([
            'wallet_id' => $this->wallet->id,
            'asset_id' => $asset->id,
            'amount' => $amount,
            'operation' => $operation,
            'balance' => $operation === Transaction::OPERATION_DEBIT
                ? (float) bcadd($this->getBalance($asset), $amount, self::COMPARISON_ACCURACY)
                : (float) bcsub($this->getBalance($asset), $amount, self::COMPARISON_ACCURACY),
        ]);
    }
}