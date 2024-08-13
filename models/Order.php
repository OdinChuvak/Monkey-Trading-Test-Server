<?php

namespace app\models;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int|null $pair_id
 * @property string|null $operation
 * @property float|null $invested_amount
 * @property float|null $received_amount
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Order extends BaseModel
{
    const OPERATION_BUY = 'buy';
    const OPERATION_SELL = 'sell';

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
            [['operation'], 'string'],
            [['invested_amount', 'received_amount'], 'number'],
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
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }
}
