<?php

namespace app\controllers;

use app\base\Controller;
use app\models\Pair;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;

class RateController extends Controller
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
                'get-actual' => ['GET'],
            ],
        ];

        return $behaviors;
    }

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