<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%property_value}}`.
 */
class m190704_000005_create_property_value_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%property_value}}', [
            'id' => $this->primaryKey(),
            'property_id' => $this->integer(),
            'object_id' => $this->integer(),
            'period' => $this->timestamp(),
            'value' => $this->string(),
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%property_value}}');
    }
}
