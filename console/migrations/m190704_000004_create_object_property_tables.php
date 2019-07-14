<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%object_property}}`.
 */
class m190704_000004_create_object_property_tables extends Migration
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
            'property_rules' => $this->json(),
            'property_label' => $this->string(50),
            'is_periodic' => $this->boolean()
        ]);

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

        $this->dropTable('{{%object_property}}');
    }
}
