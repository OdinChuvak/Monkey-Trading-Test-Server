<?php

namespace app\models;

use app\base\Model;
use app\enums\DBTables;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property string|null access_token
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property Wallet $wallet
 */
class Account extends Model implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return DBTables::ACCOUNT;
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['created_at', 'updated_at'], 'integer'],
            [['access_token'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'access_token' => 'Auth Token',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getWallet(): ActiveQuery
    {
        return $this->hasOne(Wallet::class, ['account_id' => 'id'])
            ->onCondition(['is_active' => true]);
    }

    public static function findIdentity($id): ?Account
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null): ?Account
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAuthKey(): ?string
    {
        return $this->access_token;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }
}
