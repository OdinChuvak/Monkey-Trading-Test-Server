<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%moment}}`.
 */
class m240802_105318_create_moment_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%' . MOMENT_TABLE . '}}', [
            'id' => $this->primaryKey(),
            'timestamp' => $this->integer(),
            'is_current' => $this->boolean(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%' . MOMENT_TABLE . '}}');
    }
}
