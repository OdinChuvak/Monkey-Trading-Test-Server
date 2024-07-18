<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pairs}}`.
 */
class m240718_114646_create_pairs_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%' . PAIRS_TABLE . '}}', [
            'id' => $this->primaryKey(),
            'base_currency' => $this->string('8'),
            'quoted_currency' => $this->string('8'),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%' . PAIRS_TABLE . '}}');
    }
}
