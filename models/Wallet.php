<?php

namespace app\models;

use app\base\Model;
use app\enums\DBTables;

/**
 * This is the model class for table "wallet".
 *
 * @property int $id
 * @property int $account_id
 * @property bool $is_active
 * @property int $created_at
 * @property int $updated_at
 */
class Wallet extends Model
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return DBTables::WALLET;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['account_id', 'is_active', 'created_at', 'updated_at'], 'required'],
            [['account_id', 'created_at', 'updated_at'], 'integer'],
            [['is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'account_id' => 'ID аккаунта',
            'is_active' => 'Признак активного кошелька аккаунта',
            'created_at' => 'Создано',
            'updated_at' => 'Изменено',
        ];
    }
}
