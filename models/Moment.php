<?php

namespace app\models;

use app\base\Model;
use app\enums\DBTables;
use app\common\DomainException;
use app\enums\DomainErrors;

/**
 * This is the model class for table "moment".
 *
 * @property int $id
 * @property int|null $timestamp
 */
class Moment extends Model
{
    /**
     * @var int
     */
    private static ?int $currentTime = null;

    /**
     * @var Moment
     */
    private static ?self $currentMoment = null;

    const CURRENT_TIMESTAMP_CACHE_FILE = __DIR__ . '/../runtime/app/moment.timestamp';

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
        if (self::$currentTime) {
            return self::$currentTime;
        }

        $cacheFile = self::CURRENT_TIMESTAMP_CACHE_FILE;

        if (!file_exists($cacheFile)) {
            throw new DomainException(DomainErrors::MOMENT_NOT_SPECIFIED);
        }

        $timestamp = file_get_contents($cacheFile);
        $timestamp = trim($timestamp);

        if (strlen($timestamp) !== 10 || !is_numeric($timestamp)) {
            throw new DomainException(DomainErrors::INCORRECT_TIMESTAMP);
        }

        self::$currentTime = (int) $timestamp;

        return (int) $timestamp;
    }

    /**
     * @throws DomainException
     */
    public static function getCurrentMoment(): Moment
    {
        if (self::$currentMoment) {
            return self::$currentMoment;
        }

        self::$currentMoment = Moment::findOne(['timestamp' => self::getCurrentTimestamp()]);

        if (!self::$currentMoment) {
            throw new DomainException(DomainErrors::MOMENT_MISSING);
        }

        return self::$currentMoment;
    }
}