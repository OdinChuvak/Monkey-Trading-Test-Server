<?php

namespace app\models;

use app\base\Model;
use app\enums\DBTables;
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
 * @property Moment $moment
 */
class Rate extends Model
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return DBTables::RATE;
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

    /**
     * @return ActiveQuery
     */
    public function getMoment(): ActiveQuery
    {
        return $this->hasOne(Moment::class, ['id' => 'moment_id']);
    }
}
