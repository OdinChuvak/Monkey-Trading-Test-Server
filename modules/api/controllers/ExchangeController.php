<?php

namespace app\modules\api\controllers;

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

    public function actionGetPairs()
    {
        return [
            [
                'base_currency' => '',
                'quoted_currency' => '',
            ]
        ];
    }

    public function actionGetPairConfigurations()
    {
        return [
            [
                'base_currency' => '',
                'quoted_currency' => '',
                'min_price' => '',
                'max_price' => '',
                'price_step' => '',
                'price_precision' => '',
                'min_quantity' => '',
                'max_quantity' => '',
                'quantity_step' => '',
                'quantity_precision' => '',
            ]
        ];
    }

    public function actionGetCommissions()
    {
        return [
            [
                'symbol' => '',
                'sell_commission' => '',
                'buy_commission' => '',
            ]
        ];
    }
}