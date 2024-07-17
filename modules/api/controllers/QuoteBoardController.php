<?php

namespace app\modules\api\controllers;

use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Response;

class QuoteBoardController extends BaseController
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbFilter'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'index' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function actionIndex(): array
    {
        return [
            [
                'symbol' => '',
                'price' => '',
            ]
        ];
    }
}