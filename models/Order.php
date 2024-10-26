<?php

namespace app\models;

use app\base\Model;
use app\enums\DBTables;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int|null $pair_id
 * @property string|null $operation
 * @property float|null $invested_amount
 * @property float|null $received_amount
 * @property float|null $rate
 * @property int $commission_asset_id
 * @property float|null $commission_amount
 * @property string $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property Pair $pair
 * @property Asset $commissionAsset
 */
class Order extends Model
{
    const OPERATION_BUY = 'BUY';
    const OPERATION_SELL = 'SELL';
    const STATUS_PLACED = 'PLACED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_FAILED = 'FAILED';
    const STATUS_EXECUTED = 'EXECUTED';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return DBTables::ORDER;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['pair_id', 'commission_asset_id', 'created_at', 'updated_at'], 'integer'],
            [['operation', 'status'], 'string'],
            [['rate', 'commission_amount', 'invested_amount', 'received_amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'pair_id' => 'ID криптовалютной пары',
            'operation' => 'Операция',
            'invested_amount' => 'Инвестированная сумма',
            'received_amount' => 'Полученная сумма',
            'rate' => 'Курс, по которому был исполнен ордер',
            'commission_asset_id' => 'ID Актива, в котором удержана комиссия',
            'commission_amount' => 'Размер комиссии',
            'status' => "ENUM('PLACES', 'EXECUTED', 'FAILED')",
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getPair(): ActiveQuery
    {
        return $this->hasOne(Pair::class, ['id' => 'pair_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCommissionAsset(): ActiveQuery
    {
        return $this->hasOne(Asset::class, ['id' => 'commission_asset_id']);
    }
}
