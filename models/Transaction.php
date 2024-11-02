<?php

namespace app\models;

use app\base\Model;
use app\enums\DBTables;

/**
 * This is the model class for table "transaction".
 *
 * @property int $id
 * @property int $wallet_id
 * @property int $asset_id
 * @property float $amount
 * @property string $operation
 * @property float $balance
 * @property int $created_at
 * @property int $updated_at
 */
class Transaction extends Model
{
    const OPERATION_DEBIT = 'debit';

    const OPERATION_CREDIT = 'credit';

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return DBTables::TRANSACTION;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['wallet_id', 'asset_id', 'amount', 'operation', 'balance', 'created_at', 'updated_at'], 'required'],
            [['wallet_id', 'asset_id', 'created_at', 'updated_at'], 'integer'],
            [['amount', 'balance'], 'number'],
            [['operation'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'wallet_id' => 'ID Кошелька',
            'asset_id' => 'ID Актива',
            'amount' => 'Сумма транзакции',
            'operation' => 'Операция: дебет или кредит',
            'balance' => 'Баланс актива в кошельке после транзакции',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }
}
