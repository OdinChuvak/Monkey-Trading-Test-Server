<?php

namespace app\models;

/**
 * This is the model class for table "rates".
 *
 * @property string|null $base_currency
 * @property string|null $quoted_currency
 * @property float|null $rate
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Rate extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return RATES_TABLE;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['rate'], 'number'],
            [['created_at', 'updated_at'], 'integer'],
            [['base_currency', 'quoted_currency'], 'string', 'max' => 8],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'base_currency' => 'Базовая криптовалюта',
            'quoted_currency' => 'Котируемая криптовалюта',
            'rate' => 'Курс',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }
}
