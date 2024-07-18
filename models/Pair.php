<?php

namespace app\models;

/**
 * This is the model class for table "pairs".
 *
 * @property int $id
 * @property string|null $base_currency
 * @property string|null $quoted_currency
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Pair extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'pairs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
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
            'id' => 'ID',
            'base_currency' => 'Базовая криптовалюта',
            'quoted_currency' => 'Котируемая криптовалюта',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }
}
