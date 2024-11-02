<?php

namespace app\base;

use app\models\Moment;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model as ModelAlias;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\db\Exception;

abstract class Model extends ActiveRecord
{
    /**
     * @throws InvalidConfigException
     */
    public static function getDb()
    {
        return Yii::$app->get('db');
    }

    /**
     * @throws \Exception
     */
    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ModelAlias::EVENT_BEFORE_VALIDATE => ['created_at', 'updated_at'],
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    BaseActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function() {
                    return Moment::getCurrentTimestamp();
                },
            ],
        ];
    }

    /**
     * @throws Exception
     */
    public static function add(array $values, string $formName = ''): Model
    {
        $model = new static();

        if (!$model->load($values, $formName) || !$model->save()) {
            $attribute = array_key_first($model->getFirstErrors());
            throw new Exception($model->getFirstError($attribute));
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