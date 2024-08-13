<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;

abstract class BaseModel extends ActiveRecord
{
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('UNIX_TIMESTAMP()'),
            ],
        ];
    }

    /**
     * @throws Exception
     */
    public static function add(array $values, string $formName = ''): ActiveRecord
    {
        $model = new static();

        $model->load($values, $formName);
        $model->save();

        if (count($model->errors) === 0) {
            $model->refresh();
        }

        return $model;
    }

    /**
     * @throws Exception
     */
    public static function batchInsert(array $data, array $attributes = null): void
    {
        $connection = static::getDb();

        $connection->createCommand()
            ->batchInsert(
                static::tableName(),
                $attributes ?: static::batchInsertAttributes(),
                $data
            )->execute();
    }
}