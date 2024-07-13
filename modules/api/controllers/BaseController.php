<?php

namespace app\modules\api\controllers;

use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\Response;

abstract class BaseController extends Controller
{
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviors;
    }
}