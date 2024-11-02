<?php

namespace app\controllers;

use app\base\Controller;
use app\models\Asset;
use yii\filters\VerbFilter;

class AssetController extends Controller
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
                'get-delisted-assets' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @return array
     */
    public function actionGetDelistedAssets(): array
    {
        return Asset::find()
            ->select('asset')
            ->where(['delisted' => 1])
            ->column();
    }
}