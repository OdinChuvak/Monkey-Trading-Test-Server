<?php

use app\enums\DBTables;
use app\base\Migration;

/**
 * Handles the creation of table `{{%transactions}}`.
 */
class m241024_161051_create_transactions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%' . DBTables::TRANSACTION . '}}', [
            'id' => $this->primaryKey(),
            'wallet_id' => $this->integer(),
            'asset_id' => $this->integer(),
            'amount' => $this->float(),
            'operation' => "ENUM('debit', 'credit')",
            'balance' => $this->float(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%' . DBTables::TRANSACTION . '}}');
    }
}
