<?php

namespace app\modules\api\controllers;

use app\models\Moment;
use app\modules\api\common\responses\ApiResponse;
use enums\ApiErrorCode;
use Yii;

class MomentController extends BaseController
{
    public function actionNext(): ApiResponse
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            /**
             * @var Moment $currentMoment
             */
            $currentMoment = Moment::find()
                ->where(['is_current' => true])
                ->one();

            /**
             * @var Moment $nextMoment
             */
            $nextMoment = Moment::find()
                ->where(['>', 'timestamp', $currentMoment->timestamp ?: 0])
                ->orderBy(['timestamp' => SORT_ASC])
                ->one();

            if (!$nextMoment) {
                return ApiResponse::getSuccessResponse([
                    'timestamp' => null,
                ]);
            }

            if ($currentMoment) {
                $currentMoment->updateAttributes([
                    'is_current' => false,
                ]);
            }

            $nextMoment->updateAttributes([
                'is_current' => true,
            ]);

            $transaction->commit();

            return ApiResponse::getSuccessResponse([
                'timestamp' => $nextMoment->timestamp,
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