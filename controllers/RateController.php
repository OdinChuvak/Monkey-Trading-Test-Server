<?php

namespace app\controllers;

use app\base\Controller;
use app\models\Pair;
use yii\base\InvalidConfigException;

class RateController extends Controller
{
    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function actionGetActual(): array
    {
        return Pair::find()
            ->onlyAvailableAssets('ba', 'qa')
            ->select(['CONCAT(ba.asset, qa.asset) as symbol', 'ba.asset as base_asset', 'qa.asset as quoted_asset', 'rate'])
            ->joinWith(['actualRate'], false)
            ->asArray()
            ->all();
    }
}