<?php

namespace app\controllers;

use app\base\Controller;
use app\models\Asset;

class AssetController extends Controller
{
    protected function safeActions(): array
    {
        return  [];
    }

    /**
     * @return array
     */
    public function getDelistedAssetsAction(): array
    {
        return Asset::find()
            ->select('asset')
            ->where(['delisted' => 1])
            ->column();
    }
}