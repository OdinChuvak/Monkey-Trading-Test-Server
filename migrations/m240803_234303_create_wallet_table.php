<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%wallet}}`.
 */
class m240803_234303_create_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%' . WALLET_TABLE . '}}', [
            'id' => $this->primaryKey(),
            'currency' => $this->string(32),
            'amount' => $this->float(12),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%' . WALLET_TABLE . '}}');
    }
}
