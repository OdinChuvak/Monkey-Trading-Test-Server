<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%rates}}`.
 */
class m240717_150313_create_rates_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%' . RATES_TABLE . '}}', [
            'base_currency' => $this->string('8'),
            'quoted_currency' => $this->string('8'),
            'rate' => $this->float(10),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%' . RATES_TABLE . '}}');
    }
}
