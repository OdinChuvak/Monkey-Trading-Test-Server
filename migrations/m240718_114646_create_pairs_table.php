<?php

use app\enums\DBTables;
use app\base\Migration;

/**
 * Handles the creation of table `{{%pair}}`.
 */
class m240718_114646_create_pairs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp(): void
    {
        $this->createTable('{{%' . DBTables::PAIR . '}}', [
            'id' => $this->primaryKey(),
            'base_asset_id' => $this->integer(),
            'quoted_asset_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown(): void
    {
        $this->dropTable('{{%' . DBTables::PAIR . '}}');
    }
}
