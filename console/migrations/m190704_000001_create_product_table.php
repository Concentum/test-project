<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product}}`.
 */
class m190704_000001_create_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product}}', [
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
            'product-parent_id-index',
            'product',
            'parent_id'
        );

        $this->addForeignKey(
            'product-parent_id-fkey',
            'product',
            'parent_id',
            'product',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'product-author_id-fkey',
            'product',
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
            'product-parent_id-fkey',
            'product'
        );

        $this->dropIndex(
            'product-parent_id-index',
            'product'
        );
        
        $this->dropTable('{{%product}}');
    }
}
