<?php

namespace app\models;

/**
 * This is the model class for table "wallet".
 *
 * @property int $id
 * @property string|null $currency
 * @property float|null $amount
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Wallet extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return WALLET_TABLE;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['amount'], 'number'],
            [['created_at', 'updated_at'], 'integer'],
            [['currency'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'currency' => 'Криптовалюта',
            'amount' => 'Сумма',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }
}
