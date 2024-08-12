<?php

namespace app\models;

/**
 * This is the model class for table "moment".
 *
 * @property int $id
 * @property int|null $timestamp
 * @property int|null $is_current
 * @property int|null $created_at
 * @property int|null $updated_at
 */
class Moment extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return MOMENT_TABLE;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['timestamp', 'is_current', 'created_at', 'updated_at'], 'integer'],
            [['timestamp'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'timestamp' => 'Временная метка момента времени',
            'is_current' => 'Признак текущей метки',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    public static function batchInsertAttributes(): array
    {
        return [
            'timestamp',
            'is_current',
            'created_at',
            'updated_at',
        ];
    }
}
