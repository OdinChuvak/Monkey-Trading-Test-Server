<?php

namespace app\controllers;

use app\base\Controller;
use app\models\Pair;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;

class PairController extends Controller
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
                'get-configurations' => ['GET'],
                'get-commissions' => ['GET'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function actionGetAll(): array
    {
        return Pair::find()
            ->onlyAvailableAssets('ba', 'qa')
            ->select(['CONCAT(ba.asset, qa.asset) as symbol', 'ba.asset as base_asset', 'qa.asset as quoted_asset'])
            ->asArray()
            ->all();
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function actionGetConfigurations(): array
    {
        $columns = [
            'CONCAT(ba.asset, qa.asset) as symbol',
            'ba.asset as base_asset',
            'qa.asset as quoted_asset',
            'min_price',
            'max_price',
            'price_step',
            'price_precision',
            'min_quantity',
            'max_quantity',
            'quantity_step',
            'quantity_precision'
        ];

        return Pair::find()
            ->onlyAvailableAssets('ba', 'qa')
            ->select($columns)
            ->joinWith('configuration', false)
            ->asArray()
            ->all();
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function actionGetCommissions(): array
    {
        return Pair::find()
            ->onlyAvailableAssets('ba', 'qa')
            ->select(['CONCAT(ba.asset, qa.asset) as symbol', 'ba.asset as base_asset', 'qa.asset as quoted_asset', 'buy_commission', 'sell_commission'])
            ->joinWith('commission', false)
            ->asArray()
            ->all();
    }
}