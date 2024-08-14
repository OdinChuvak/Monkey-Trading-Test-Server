<?php

namespace app\modules\api\controllers;

use app\enums\ApiErrorCode;
use app\models\Moment;
use app\models\Rate;
use app\modules\api\common\responses\ApiResponse;
use yii\filters\VerbFilter;

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

    public function actionIndex(): ApiResponse
    {
        $moment = Moment::findOne(['is_current' => true]);

        if (!$moment) {
            return ApiResponse::getErrorResponse(ApiErrorCode::MISSING_MOMENT);
        }

        $rates = Rate::findAll(['moment_id' => $moment->id]);

        return ApiResponse::getSuccessResponse(array_map(function (Rate $rate) {
            return [
                'base_currency' => $rate->pair->base_currency,
                'quoted_currency' => $rate->pair->quoted_currency,
                'price' => $rate->rate,
            ];
        }, $rates));
    }
}