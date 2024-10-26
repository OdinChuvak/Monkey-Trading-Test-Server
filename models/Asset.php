<?php

namespace app\models;

use app\base\Model;

/**
 * This is the model class for table "asset".
 *
 * @property int $id
 * @property string $asset
 * @property int $delisted
 * @property int $created_at
 * @property int $updated_at
 */
class Asset extends Model
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'asset';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['delisted', 'created_at', 'updated_at'], 'integer'],
            [['asset'], 'string', 'max' => 8],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'asset' => 'Актив',
            'delisted' => 'Произведен делистинг актива',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }
}
