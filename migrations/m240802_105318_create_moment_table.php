<?php

use app\enums\DBTables;
use app\base\Migration;

/**
 * Handles the creation of table `{{%moment}}`.
 */
class m240802_105318_create_moment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%' . DBTables::MOMENT . '}}', [
            'id' => $this->primaryKey(),
            'timestamp' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%' . DBTables::MOMENT . '}}');
    }
}
