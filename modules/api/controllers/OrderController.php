<?php

namespace app\modules\api\controllers;

use app\enums\ApiErrorCode;
use app\models\Moment;
use app\models\Order;
use app\models\Pair;
use app\models\Rate;
use app\models\Wallet;
use app\modules\api\common\responses\ApiResponse;
use Yii;
use yii\base\DynamicModel;
use yii\filters\VerbFilter;

class OrderController extends BaseController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbFilter'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'create' => ['POST'],
                'get-info' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionGetInfo($id): ApiResponse
    {
        /**
         * @var Order $order
         */
        $order = Order::find()
            ->with('pair')
            ->where(['id' => $id])
            ->one();

        if (!$order) {
            return ApiResponse::getErrorResponse(ApiErrorCode::MISSING_ORDER);
        }

        return ApiResponse::getSuccessResponse([
            'id' => $order->id,
            'status' => Order::STATUS_EXECUTED,
            'operation' => $order->operation,
            'rate' => $order->rate,
            'base_currency' => $order->pair->base_currency,
            'quoted_currency' => $order->pair->quoted_currency,
            'commission_currency' => $order->commission_asset,
            'commission' => $order->commission,
            'invested' => $order->invested_amount,
            'received' => $order->received_amount,
        ]);
    }

    public function actionCreate(): ApiResponse
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            [
                'operation' => $operation,
                'base_currency' => $baseCurrency,
                'quoted_currency' => $quotedCurrency,
                'amount' => $amount,
            ] = Yii::$app->request->getBodyParams();

            $validationModel = DynamicModel::validateData(compact('operation', 'baseCurrency', 'quotedCurrency', 'amount'), [
                [['operation', 'baseCurrency', 'quotedCurrency', 'amount'], 'required'],
                [['operation', 'baseCurrency', 'quotedCurrency'], 'string'],
                [['amount'], 'double'],
            ]);

            if ($validationModel->hasErrors()) {
                return ApiResponse::getErrorResponse([
                    'message' => array_shift($validationModel->errors)[0],
                    'code' => ApiErrorCode::CUSTOM_ERROR_CODE,
                ]);
            }

            $wallet = Wallet::find()
                ->indexBy('currency')
                ->where([
                    'currency' => [$baseCurrency, $quotedCurrency],
                ])->all();

            /**
             * @var Wallet $quotedCurrencyBalance
             * @var Wallet $baseCurrencyBalance
             */
            $quotedCurrencyBalance = $wallet[$quotedCurrency] ?: Wallet::add([
                'currency' => $quotedCurrency,
                'amount' => 0,
            ]);

            $baseCurrencyBalance = $wallet[$baseCurrency] ?: Wallet::add([
                'currency' => $baseCurrency,
                'amount' => 0,
            ]);

            if (($operation === Order::OPERATION_BUY && $quotedCurrencyBalance->amount < $amount)
                || ($operation === Order::OPERATION_SELL && $baseCurrencyBalance->amount < $amount))
            {
                return ApiResponse::getErrorResponse(ApiErrorCode::INSUFFICIENT_FUND);
            }

            /**
             * @var Pair $pair
             */
            $pair = Pair::find()
                ->with('commission', 'configuration')
                ->where(['base_currency' => $baseCurrency, 'quoted_currency' => $quotedCurrency])
                ->one();

            if (!$pair) {
                return ApiResponse::getErrorResponse(ApiErrorCode::UNAVAILABLE_CURRENCY_PAIR);
            }

            $moment = Moment::findOne(['is_current' => true]);

            if (!$moment) {
                return ApiResponse::getErrorResponse(ApiErrorCode::MISSING_MOMENT);
            }

            $actualRate = Rate::findOne([
                'pair_id' => $pair->id,
                'moment_id' => $moment->id,
            ]);

            if (!$actualRate) {
                return ApiResponse::getErrorResponse(ApiErrorCode::MISSING_RATE);
            }
            if ($operation === Order::OPERATION_BUY) {
                $value = ($amount / $actualRate->rate);
                $commission = $value * $pair->commission->buy_commission;
                $commissionAsset = $pair->base_currency;
                $value = $value - $commission;
                $baseCurrencyBalance->amount += $value;
                $quotedCurrencyBalance->amount -= $amount;
            } else {
                $value = ($amount / (1 / $actualRate->rate));
                $commission = $value * $pair->commission->sell_commission;
                $commissionAsset = $pair->quoted_currency;
                $value = $value - $commission;
                $baseCurrencyBalance->amount -= $amount;
                $quotedCurrencyBalance->amount += $value;
            }

            $baseCurrencyBalance->save();
            $quotedCurrencyBalance->save();

            /**
             * @var Order $order
             */
            $order = Order::add([
                'pair_id' => $pair->id,
                'operation' => $operation,
                'invested_amount' => $amount,
                'received_amount' => $value,
                'rate' => $actualRate->rate,
                'commission_asset' => $commissionAsset,
                'commission' => $commission,
                'status' => Order::STATUS_EXECUTED,
            ]);

            $transaction->commit();

            return ApiResponse::getSuccessResponse([
                'external_id' => $order->id ?: null,
            ]);
        } catch (\Exception $exception) {
            $transaction->rollBack();

            return ApiResponse::getErrorResponse([
                'code' => ApiErrorCode::CUSTOM_ERROR_CODE,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}