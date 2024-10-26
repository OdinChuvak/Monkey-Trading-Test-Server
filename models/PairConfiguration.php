<?php

namespace app\models;

use app\base\Model;
use app\enums\DBTables;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "pair_configuration".
 *
 * @property int $id
 * @property int|null $pair_id
 * @property float|null $min_price
 * @property float|null $max_price
 * @property float|null $price_step
 * @property float|null $price_precision
 * @property float|null $min_quantity
 * @property float|null $max_quantity
 * @property float|null $quantity_step
 * @property float|null $quantity_precision
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property Pair $pair
 */
class PairConfiguration extends Model
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return DBTables::PAIR_CONFIGURATION;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['pair_id', 'created_at', 'updated_at'], 'integer'],
            [['min_price', 'max_price', 'price_step', 'price_precision', 'min_quantity', 'max_quantity', 'quantity_step', 'quantity_precision'], 'number'],
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
            'min_price' => 'Минимальная цена',
            'max_price' => 'Максимальная цена',
            'price_step' => 'Шаг цены',
            'price_precision' => 'Точность цены',
            'min_quantity' => 'Минимальное количество',
            'max_quantity' => 'Максимальное количество',
            'quantity_step' => 'Шаг количества',
            'quantity_precision' => 'Точность количества',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public static function batchInsertAttributes(): array
    {
        return [
            'pair_id',
            'min_price',
            'max_price',
            'price_step',
            'price_precision',
            'min_quantity',
            'max_quantity',
            'quantity_step',
            'quantity_precision',
            'created_at',
            'updated_at',
        ];
    }

    public function getPair(): ActiveQuery
    {
        return $this->hasOne(Pair::class, ['id' => 'pair_id']);
    }
}
