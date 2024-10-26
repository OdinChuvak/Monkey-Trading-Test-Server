<?php

use app\enums\DBTables;
use app\base\Migration;

/**
 * Handles the creation of table `{{%account}}`.
 */
class m241019_141714_create_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%' . DBTables::ACCOUNT . '}}', [
            'id' => $this->primaryKey(),
            'access_token' => $this->string(128),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%' . DBTables::ACCOUNT . '}}');
    }
}
