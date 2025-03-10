<?php

use app\enums\DBTables;
use app\base\Migration;

/**
 * Handles the creation of table `{{%order}}`.
 */
class m240803_233636_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%' . DBTables::ORDER . '}}', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'pair_id' => $this->integer(),
            'operation' => "ENUM('sell', 'buy')",
            'invested_amount' => $this->float(12),
            'received_amount' => $this->float(12),
            'rate' => $this->float(12),
            'commission_asset_id' => $this->integer(),
            'commission_amount' => $this->float(12),
            'status' => "ENUM('PLACES', 'EXECUTED', 'FAILED', 'CANCELLED')",
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%' . DBTables::ORDER . '}}');
    }
}
