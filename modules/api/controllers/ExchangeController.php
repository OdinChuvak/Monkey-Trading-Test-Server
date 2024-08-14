<?php

namespace app\modules\api\controllers;

use app\models\Pair;
use app\models\PairCommission;
use app\models\PairConfiguration;
use app\modules\api\common\responses\ApiResponse;
use yii\filters\VerbFilter;
use yii\rest\Controller;

class ExchangeController extends BaseController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbFilter'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'get-pairs' => ['GET'],
                'get-pair-configurations' => ['GET'],
                'get-commissions' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionGetPairs(): ApiResponse
    {
        return ApiResponse::getSuccessResponse(array_map(function($pair) {
            /**
             * @var Pair $pair
             */
            return [
                'base_currency' => $pair->base_currency,
                'quoted_currency' => $pair->quoted_currency,
            ];
        }, Pair::find()->all()));
    }

    public function actionGetConfigurations(): ApiResponse
    {
        $configurations = PairConfiguration::find()
            ->with('pair')
            ->all();

        return ApiResponse::getSuccessResponse(array_map(function($configuration) {

            /**
             * @var PairConfiguration $configuration
             */
            return [
                'base_currency' => $configuration->pair->base_currency,
                'quoted_currency' => $configuration->pair->quoted_currency,
                'min_price' => $configuration->min_price,
                'max_price' => $configuration->max_price,
                'price_step' => $configuration->price_step,
                'price_precision' => $configuration->price_precision,
                'min_quantity' => $configuration->min_quantity,
                'max_quantity' => $configuration->max_quantity,
                'quantity_step' => $configuration->quantity_step,
                'quantity_precision' => $configuration->quantity_precision,
            ];
        }, $configurations));
    }

    public function actionGetCommissions(): ApiResponse
    {
        $symbols = \Yii::$app->request->getQueryParam('symbols');

        $query = PairCommission::find()
            ->joinWith('pair');

        if (!empty($symbols)) {
            $query = $query
                ->where(["CONCAT(`pair`.`base_currency`, `pair`.`quoted_currency`)" => $symbols]);
        }

        $commissions = $query->all();

        return ApiResponse::getSuccessResponse(array_map(function($commission) {

            /**
             * @var PairCommission $commission
             */
            return [
                'base_currency' => $commission->pair->base_currency,
                'quoted_currency' => $commission->pair->quoted_currency,
                'sell_commission' => $commission->sell_commission,
                'buy_commission' => $commission->buy_commission,
            ];
        }, $commissions));
    }
}