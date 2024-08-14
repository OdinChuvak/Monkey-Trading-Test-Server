<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "rates".
 *
 * @property int $id
 * @property int $pair_id
 * @property int $moment_id
 * @property float|null $rate
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property Pair $pair
 */
class Rate extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return RATE_TABLE;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['rate'], 'number'],
            [['pair_id', 'created_at', 'updated_at'], 'integer'],
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
            'moment_id' => 'ID момента',
            'rate' => 'Курс',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public static function batchInsertAttributes(): array
    {
        return [
            'pair_id',
            'moment_id',
            'rate',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPair(): ActiveQuery
    {
        return $this->hasOne(Pair::class, ['id' => 'pair_id']);
    }
}
