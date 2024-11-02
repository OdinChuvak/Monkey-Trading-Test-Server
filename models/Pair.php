<?php

namespace app\models;

use app\base\Model;
use app\common\DomainException;
use app\enums\DBTables;
use app\queries\PairQuery;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "pairs".
 *
 * @property int $id
 * @property int $base_asset_id
 * @property int $quoted_asset_id
 * @property int $created_at
 * @property int $updated_at
 * @property PairCommission $commission
 * @property PairConfiguration $configuration
 * @property Rate $actualRate
 * @property Asset $baseAsset
 * @property Asset $quotedAsset
 */
class Pair extends Model
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return DBTables::PAIR;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at', 'updated_at', 'base_asset_id', 'quoted_asset_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'base_asset_id' => 'ID базового актива',
            'quoted_asset_id' => 'ID котируемого актива',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public static function find(): PairQuery
    {
        return Yii::createObject(PairQuery::class, [get_called_class()]);
    }

    public function getCommission(): ActiveQuery
    {
        return $this->hasOne(PairCommission::class, ['pair_id' => 'id']);
    }

    public function getConfiguration(): ActiveQuery
    {
        return $this->hasOne(PairConfiguration::class, ['pair_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     * @throws DomainException
     */
    public function getActualRate(): ActiveQuery
    {
        return $this->hasOne(Rate::class, ['pair_id' => 'id'])
            ->where(['moment_id' => (Moment::getCurrentMoment())->id]);
    }

    /**
     * @return ActiveQuery
     */
    public function getBaseAsset(): ActiveQuery
    {
        return $this->hasOne(Asset::class, ['id' => 'base_asset_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getQuotedAsset(): ActiveQuery
    {
        return $this->hasOne(Asset::class, ['id' => 'quoted_asset_id']);
    }
}
