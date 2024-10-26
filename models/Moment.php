<?php

namespace app\models;

use app\base\Model;
use app\enums\DBTables;
use app\common\DomainException;
use app\enums\DomainErrors;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "moment".
 *
 * @property int $id
 * @property int|null $timestamp
 */
class Moment extends Model
{
    const CURRENT_TIMESTAMP_CACHE_FILE = __DIR__ . '/moment.timestamp';

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
                'value' => time(),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return DBTables::MOMENT;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['timestamp'], 'integer'],
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
        ];
    }

    public static function batchInsertAttributes(): array
    {
        return [
            'timestamp',
        ];
    }

    /**
     * @return int
     * @throws DomainException
     */
    public static function getCurrentTimestamp(): int
    {
        $cacheFile = self::CURRENT_TIMESTAMP_CACHE_FILE;

        if (!file_exists($cacheFile)) {
            throw new DomainException(DomainErrors::MOMENT_NOT_SPECIFIED);
        }

        $timestamp = file_get_contents($cacheFile);
        $timestamp = trim($timestamp);

        if (strlen($timestamp) !== 10 || !is_numeric($timestamp)) {
            throw new DomainException(DomainErrors::INCORRECT_TIMESTAMP);
        }

        return (int) $timestamp;
    }
}