<?php

namespace app\queries;

use yii\db\ActiveQuery;

class PairQuery extends ActiveQuery
{
    public function joinWithAssets(string $baseAssetAlias = null, string $quotedAssetAlias = null): PairQuery
    {
        return $this->joinWith([
            'baseAsset' . ($baseAssetAlias ? ' as ' . $baseAssetAlias : ''),
            'quotedAsset as qa' . ($quotedAssetAlias ? ' as ' . $quotedAssetAlias : '')]);
    }

    /**
     * @return PairQuery
     */
    public function onlyAvailableAssets(string $baseAssetAlias = null, string $quotedAssetAlias = null): PairQuery
    {
        return $this->joinWith([
                'baseAsset' . ($baseAssetAlias ? ' as ' . $baseAssetAlias : ''),
                'quotedAsset' . ($quotedAssetAlias ? ' as ' . $quotedAssetAlias : '')
            ], false)
            ->andWhere([
                'AND',
                [($baseAssetAlias ?: 'baseAsset') . '.delisted' => [0, null]],
                [($quotedAssetAlias ?: 'quotedAsset') . '.delisted' => [0, null]],
            ]);
    }
}