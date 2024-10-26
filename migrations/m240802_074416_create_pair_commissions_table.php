<?php

use app\enums\DBTables;
use app\base\Migration;

/**
 * Handles the creation of table `{{%pair_commission}}`.
 */
class m240802_074416_create_pair_commissions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%' . DBTables::PAIR_COMMISSION . '}}', [
            'id' => $this->primaryKey(),
            'pair_id' => $this->integer(),
            'buy_commission' => $this->float('8'),
            'sell_commission' => $this->float('8'),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%' . DBTables::PAIR_COMMISSION . '}}');
    }
}
