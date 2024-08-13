<?php

namespace app\models;

use yii\db\ActiveQuery;

/**
 * This is the model class for table "pairs".
 *
 * @property int $id
 * @property string|null $base_currency
 * @property string|null $quoted_currency
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property PairCommission $commission
 * @property PairConfiguration $configuration
 */
class Pair extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return PAIR_TABLE;
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

    public function getCommission(): ActiveQuery
    {
        return $this->hasOne(PairCommission::class, ['pair_id' => 'id']);
    }

    public function getConfiguration(): ActiveQuery
    {
        return $this->hasOne(PairConfiguration::class, ['pair_id' => 'id']);
    }
}
