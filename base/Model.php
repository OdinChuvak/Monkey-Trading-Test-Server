<?php

namespace app\base;

use app\models\Moment;
use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;

abstract class Model extends ActiveRecord
{
    /**
     * @throws \Exception
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => Moment::getCurrentTimestamp(),
            ],
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    /**
     * @throws Exception
     */
    public static function add(array $values, string $formName = ''): Model
    {
        $model = new static();

        if (!$model->load($values, $formName) || !$model->save()) {
            throw new Exception(reset($model->firstErrors));
        }

        return $model;
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
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