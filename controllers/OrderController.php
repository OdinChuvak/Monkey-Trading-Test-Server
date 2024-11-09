<?php

namespace app\controllers;

use app\base\Controller;
use app\common\DomainException;
use app\enums\DomainErrors;
use app\managers\WalletManager;
use app\models\Pair;
use app\models\Order;
use app\models\Transaction;
use Yii;
use app\common\DynamicModel;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\filters\VerbFilter;

class OrderController extends Controller
{
    protected function privateActions(): array
    {
        return [
            'get-info',
            'create',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'get-info' => ['GET'],
                'create' => ['POST'],
            ],
        ];

        return $behaviors;
    }

    protected function safeActions(): array
    {
        return [
            'create',
        ];
    }

    /**
     * @param int $orderId
     * @return array
     * @throws DomainException
     * @throws InvalidConfigException
     */
    public function actionGetInfo(): array
    {
        /**
         * @var int $orderId
         */
        extract(Yii::$app->request->getQueryParams());

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
     * @return array
     * @throws DomainException
     * @throws Exception
     * @throws InvalidConfigException
     * @throws \Exception
     */
    public function actionCreate(): array
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

        if (!$requestModel->validate() && $requestModel->hasErrors()) {
            $attribute = array_key_first($requestModel->getFirstErrors());
            throw new \Exception($requestModel->getFirstError($attribute));
        }

        $walletManager = new WalletManager(\Yii::$app->user->identity->wallet);

        switch ($operation) {
            case Order::OPERATION_BUY:
                $step = $pair->configuration->price_step;
                $targetAsset = $pair->baseAsset;
                $originalAsset = $pair->quotedAsset;
                $targetValuePrecision = $pair->configuration->quantity_precision;
                $actualRate = $pair->actualRate->rate;
                $commissionRatio = $pair->commission->buy_commission;
                break;
            case Order::OPERATION_SELL:
                $step = $pair->configuration->quantity_step;
                $targetAsset = $pair->quotedAsset;
                $originalAsset = $pair->baseAsset;
                $targetValuePrecision = $pair->configuration->price_precision;
                $actualRate = 1 / $pair->actualRate->rate;
                $commissionRatio = $pair->commission->sell_commission;
                break;
        }

        if (($amount / $step) != intval($amount / $step)) {
            throw new DomainException(DomainErrors::WRONG_ORDER_AMOUNT_STEP);
        }

        $value = round($amount / $actualRate, $targetValuePrecision);
        $commission = round($value * $commissionRatio, $targetValuePrecision);

        if (!$walletManager->checkBalance($originalAsset, $amount)) {
            throw new DomainException(DomainErrors::INSUFFICIENT_FUND);
        }

        /**
         * @var Order $order
         */
        $order = Order::add([
            'account_id' => Yii::$app->user->identity->getId(),
            'pair_id' => $pair->id,
            'operation' => $operation,
            'invested_amount' => $amount,
            'received_amount' => $value - $commission,
            'rate' => $pair->actualRate->rate,
            'commission_asset_id' => $targetAsset->id,
            'commission_amount' => $commission,
            'status' => Order::STATUS_EXECUTED,
        ]);

        $walletManager->add($originalAsset, $amount, Transaction::OPERATION_CREDIT);
        $walletManager->add($targetAsset, $value - $commission, Transaction::OPERATION_DEBIT);

        return [
            'orderId' => $order->id,
            'invested' => $amount,
            'received' => $value - $commission,
            'commission_asset' => $targetAsset->asset,
            'commission_amount' => $commission,
            'status' => Order::STATUS_PLACED,
        ];
    }
}