<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int|null $pair_id
 * @property string|null $operation
 * @property float|null $invested_amount
 * @property float|null $received_amount
 * @property float|null $rate
 * @property string $commission_asset
 * @property float|null $commission
 * @property string $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property Pair $pair
 */
class Order extends BaseModel
{
    const OPERATION_BUY = 'buy';
    const OPERATION_SELL = 'sell';
    const STATUS_PLACED = 'PLACED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_FAILED = 'FAILED';
    const STATUS_EXECUTED = 'EXECUTED';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return ORDER_TABLE;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['pair_id', 'created_at', 'updated_at'], 'integer'],
            [['operation', 'commission_asset', 'status'], 'string'],
            [['rate', 'commission', 'invested_amount', 'received_amount'], 'number'],
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
            'operation' => 'Операция',
            'invested_amount' => 'Инвестированная сумма',
            'received_amount' => 'Полученная сумма',
            'rate' => 'Курс, по которому был исполнен ордер',
            'commission_asset' => 'Актив, в котором удержана комиссия',
            'commission' => 'Размер комиссии',
            'status' => "ENUM('PLACES', 'EXECUTED', 'FAILED')",
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
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
