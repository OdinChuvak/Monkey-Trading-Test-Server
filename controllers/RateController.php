<?php

namespace app\controllers;

use app\base\Controller;
use app\models\Pair;
use yii\base\InvalidConfigException;

class RateController extends Controller
{
    protected function safeActions(): array
    {
        return [];
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getActual(): array
    {
        return Pair::find()
            ->onlyAvailableAssets('ba', 'qa')
            ->select(['CONCAT(ba.asset, qa.asset) as symbol', 'ba.asset as base_asset', 'qa.asset as quoted_asset', 'timestamp', 'rate'])
            ->joinWith('actualRate.moment')
            ->all();
    }
}