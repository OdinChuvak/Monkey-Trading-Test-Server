<?php

use app\enums\DBTables;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%assets}}`.
 */
class m241029_195949_create_assets_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%' . DBTables::ASSET . '}}', [
            'id' => $this->primaryKey(),
            'asset' => $this->string(8),
            'delisted' => $this->boolean(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%' . DBTables::ASSET . '}}');
    }
}
