<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%counterparty}}`.
 */
class m190704_000003_create_counterparty_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%counterparty}}', [
            'id' => $this->primaryKey(),
            'is_deleted' => $this->boolean(),
            'is_folder' => $this->boolean(),
            'parent_id' => $this->integer(),
            'version' => $this->timestamp(),
            'code' => $this->string(12)->notNull(),
            'description' => $this->string(64)->notNull(),
            'author_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'counterparty-parent_id-index',
            'counterparty',
            'parent_id'
        );

        $this->addForeignKey(
            'counterparty-parent_id-fkey',
            'counterparty',
            'parent_id',
            'counterparty',
            'id',
            'CASCADE'
        );
        
        $this->addForeignKey(
            'counterparty-author_id-fkey',
            'counterparty',
            'author_id',
            'user',
            'id',
            'CASCADE'
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'counterparty-parent_id-fkey',
            'counterparty'
        );

        $this->dropIndex(
            'counterparty-parent_id-index',
            'counterparty'
        );
        
        $this->dropTable('{{%counterparty}}');
    }
}
