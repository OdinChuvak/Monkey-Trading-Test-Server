<?php

namespace app\controllers;

use app\base\Controller;
use app\common\DomainException;
use app\common\Response;
use app\enums\DomainErrors;
use app\models\Moment;
use yii\filters\VerbFilter;

class MomentController extends Controller
{
    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['verbs'] = [
            'class' => VerbFilter::class,
            'actions' => [
                'set-next-moment' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @return int
     * @throws DomainException
     */
    public function actionSetNextMoment(): int
    {
        $cacheFile = Moment::CURRENT_TIMESTAMP_CACHE_FILE;
        $currentTimestamp = file_exists($cacheFile) ? Moment::getCurrentTimestamp() : null;

        /**
         * @var Moment $moment
         */
        $nextMomentQuery = Moment::find();

        if ($currentTimestamp) {
            $nextMomentQuery = $nextMomentQuery->where(['>', 'timestamp', $currentTimestamp]);
        }

        /**
         * @var Moment $nextMoment
         */
        $nextMoment = $nextMomentQuery
            ->orderBy(['timestamp' => SORT_ASC])
            ->one();

        if (!$nextMoment) {
            throw new DomainException(DomainErrors::MOMENT_MISSING);
        }

        file_put_contents($cacheFile, $nextMoment->timestamp);

        return $nextMoment->timestamp;
    }
}