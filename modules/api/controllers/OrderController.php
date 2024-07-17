<?php

namespace app\modules\api\controllers;

use yii\filters\VerbFilter;
use yii\rest\Controller;

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

    public function actionGetInfo($externalId): array
    {
        return [
            'externalId' => '',
            'status' => '',
            'rate' => '',
            'invested' => '',
            'received' => ''
        ];
    }

    public function actionCreate(): array
    {
        return [
            'external_id' => ''
        ];
    }
}