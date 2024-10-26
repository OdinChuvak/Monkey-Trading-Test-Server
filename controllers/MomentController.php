<?php

namespace app\controllers;

use app\base\Controller;
use app\common\DomainException;
use app\common\Response;
use app\enums\DomainErrors;
use app\models\Moment;

class MomentController extends Controller
{
    /**
     * @return array
     */
    protected function safeActions(): array
    {
        return [];
    }

    /**
     * @return string
     * @throws DomainException
     */
    public function setNextMomentAction(): string
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

        return Response::STATUS_OK;
    }
}