<?php

use app\enums\DBTables;
use app\base\Migration;

/**
 * Handles the creation of table `{{%wallet}}`.
 */
class m240803_234303_create_wallet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%' . DBTables::WALLET . '}}', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'is_active' => $this->boolean(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%' . DBTables::WALLET . '}}');
    }
}
