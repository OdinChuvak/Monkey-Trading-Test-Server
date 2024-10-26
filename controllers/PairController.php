<?php

namespace app\controllers;

use app\models\Pair;
use yii\base\InvalidConfigException;

class PairController
{
    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getAllAction(): array
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
    public function getConfigurationsAction(): array
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
            ->joinWith('configuration')
            ->asArray()
            ->all();
    }

    /**
     * @return array
     * @throws InvalidConfigException
     */
    public function getCommissionsAction(): array
    {
        return Pair::find()
            ->onlyAvailableAssets('ba', 'qa')
            ->select(['CONCAT(ba.asset, qa.asset) as symbol', 'ba.asset as base_asset', 'qa.asset as quoted_asset', 'buy_commission', 'sell_commission'])
            ->joinWith('commission')
            ->asArray()
            ->all();
    }
}