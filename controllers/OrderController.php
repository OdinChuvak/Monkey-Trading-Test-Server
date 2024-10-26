<?php

namespace app\controllers;

use app\base\Controller;
use app\common\DomainException;
use app\common\Response;
use app\enums\DomainErrors;
use app\managers\WalletManager;
use app\models\Pair;
use app\models\Order;
use Yii;
use app\common\DynamicModel;
use yii\base\InvalidConfigException;

class OrderController extends Controller
{
    protected function safeActions(): array
    {
        return [
            'add',
        ];
    }

    /**
     * @param int $orderId
     * @return array
     * @throws DomainException
     * @throws InvalidConfigException
     */
    public function getInfoAction(int $orderId): array
    {
        /**
         * @var int $orderId
         */
        extract(Yii::$app->request->getBodyParams());

        $order = Order::findOne($orderId);

        if (!$order) {
            throw new DomainException(DomainErrors::ORDER_NOT_FOUND);
        }

        return [
            'id' => $order->id,
            'status' => $order->status,
            'operation' => $order->operation,
            'rate' => $order->rate,
            'base_asset' => $order->pair->baseAsset->asset,
            'quoted_asset' => $order->pair->quotedAsset->asset,
            'commission_asset' => $order->commissionAsset->asset,
            'commission_amount' => $order->commission_amount,
            'invested' => $order->invested_amount,
            'received' => $order->received_amount,
        ];
    }

    /**
     * @throws InvalidConfigException
     * @throws \Exception
     * @return int
     */
    public function addAction(): int
    {
        /**
         * @var string $symbol
         * @var string $operation
         * @var float $amount
         */
        extract(Yii::$app->request->getBodyParams());

        /**
         * @var Pair $pair
         */
        $pair = Pair::find()
            ->with(['commission', 'configuration'])
            ->onlyAvailableAssets('ba', 'qa')
            ->where(['CONCAT(ba.asset, qa.asset)' => $symbol])
            ->one();

        $requestModel = new DynamicModel([
            'pair' => $pair,
            'commission' => $pair->commission,
            'configuration' => $pair->configuration,
            'rate' => $pair->actualRate,
            'operation' => $operation,
            'amount' => $amount,
        ]);

        $requestModel->addRules([
            [['pair', 'commission', 'configuration', 'rate', 'operation', 'amount'], 'required'],
            [['amount'], 'double'],
            [['operation'], 'in', 'range' => [Order::OPERATION_BUY, Order::OPERATION_SELL]]
        ]);

        if (!$requestModel->validate() && $dynamicModel->hasErrors()) {
            throw new \Exception($dynamicModel->getFirstErrors()[0]);
        }

        $walletManager = new WalletManager(\Yii::$app->user->wallet);

        if ($operation === Order::OPERATION_BUY) {
            $amount = round($amount, $pair->configuration->price_precision);
            $targetAsset = $pair->baseAsset;
            $originalAsset = $pair->quotedAsset;
            $value = round($amount / $pair->actualRate->rate, $pair->configuration->quantity_precision);
            $commission = round($value * $pair->commission->buy_commission, $pair->configuration->quantity_precision);
        } else {
            $amount = round($amount, $pair->configuration->quantity_precision);
            $targetAsset = $pair->quotedAsset;
            $originalAsset = $pair->baseAsset;
            $value = round($amount / (1 / $pair->actualRate->rate), $pair->configuration->price_precision);
            $commission = round($value * $pair->commission->sell_commission, $pair->configuration->price_precision);
        }

        if (!$walletManager->checkBalance($originalAsset, $amount)) {
            throw new DomainException(DomainErrors::INSUFFICIENT_FUND);
        }

        /**
         * @var Order $order
         */
        $order = Order::add([
            'pair_id' => $pair->id,
            'operation' => $operation,
            'invested_amount' => $amount,
            'received_amount' => $value - $commission,
            'rate' => $pair->actualRate->rate,
            'commission_asset_id' => $targetAsset->id,
            'commission_amount' => $commission,
            'status' => Order::STATUS_EXECUTED,
        ]);

        $walletManager->credit($originalAsset, $amount);
        $walletManager->debit($targetAsset, $value - $commission);

        return $order->id;
    }
}