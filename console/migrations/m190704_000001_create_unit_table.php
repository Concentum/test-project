<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%unit}}`.
 */
class m190704_000001_create_unit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%unit}}', [
            'id' => $this->primaryKey(),
            'is_deleted' => $this->boolean(),
            'version' => $this->timestamp(),
            'code' => $this->string(12)->notNull(),
            'description' => $this->string(64)->notNull(),
            'designation' => $this->string(16)->notNull(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%unit}}');
    }
}
