<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%pair_configuration}}`.
 */
class m240802_073958_create_pair_configurations_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%' . PAIR_CONFIGURATION_TABLE . '}}', [
            'id' => $this->primaryKey(),
            'pair_id' => $this->integer(),
            'min_price' => $this->float(10),
            'max_price' => $this->float(10),
            'price_step' => $this->float(10),
            'price_precision' => $this->float(10),
            'min_quantity' => $this->float(10),
            'max_quantity' => $this->float(10),
            'quantity_step' => $this->float(10),
            'quantity_precision' => $this->float(10),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%' . PAIR_CONFIGURATION_TABLE . '}}');
    }
}
