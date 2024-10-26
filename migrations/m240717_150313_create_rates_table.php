<?php

use app\enums\DBTables;
use app\base\Migration;

/**
 * Handles the creation of table `{{%rate}}`.
 */
class m240717_150313_create_rates_table extends Migration
{
    const INDEX_NAME = 'idx_moment_id';
    const INDEX_FIELD = 'moment_id';

    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%' . DBTables::RATE . '}}', [
            'id' => $this->primaryKey(),
            'pair_id' => $this->integer(),
            'moment_id' => $this->integer(),
            'rate' => $this->float(10),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createIndex(self::INDEX_NAME,
            DBTables::RATE,
            self::INDEX_FIELD);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%' . DBTables::RATE . '}}');
        $this->dropIndex(self::INDEX_NAME, DBTables::RATE);
    }
}
