<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%object_property}}`.
 */
class m190704_000004_create_object_property_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%object_property}}', [
            'id' => $this->primaryKey(),
            'object_class' => $this->string(),
            'property_name' => $this->string(50),
            'property_rules' => $this->string(255),
            'property_label' => $this->string(50),
            'is_periodic' => $this->boolean()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%object_property}}');
    }
}
