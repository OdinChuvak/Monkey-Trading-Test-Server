<?php

namespace app\models;

use app\base\Model;
use app\enums\DBTables;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "pair_commission".
 *
 * @property int $id
 * @property int|null $pair_id
 * @property float|null $buy_commission
 * @property float|null $sell_commission
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property Pair $pair
 */
class PairCommission extends Model
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return DBTables::PAIR_COMMISSION;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['pair_id', 'created_at', 'updated_at'], 'integer'],
            [['buy_commission', 'sell_commission'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'pair_id' => 'ID криптовалютной пары',
            'buy_commission' => 'Комиссия за покупку',
            'sell_commission' => 'Комиссия за продажу',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public static function batchInsertAttributes(): array
    {
        return [
            'pair_id',
            'buy_commission',
            'sell_commission',
            'created_at',
            'updated_at',
        ];
    }

    public function getPair(): ActiveQuery
    {
        return $this->hasOne(Pair::class, ['id' => 'pair_id']);
    }
}
