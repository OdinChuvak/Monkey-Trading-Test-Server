<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order}}`.
 */
class m240803_233636_create_orders_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%' . ORDER_TABLE . '}}', [
            'id' => $this->primaryKey(),
            'pair_id' => $this->integer(),
            'operation' => "ENUM('sell', 'buy')",
            'invested_amount' => $this->float(12),
            'received_amount' => $this->float(12),
            'rate' => $this->float(12),
            'commission_asset' => $this->string(32),
            'commission' => $this->float(12),
            'status' => "ENUM('PLACES', 'EXECUTED', 'FAILED', 'CANCELLED')",
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%' . ORDER_TABLE . '}}');
    }
}
