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
                'get-all' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    public function getAll(): array
    {
        return Asset::find()
            ->select('asset', 'IF (delisted, FALSE, TRUE) as available')
            ->asArray()
            ->all();
    }
}