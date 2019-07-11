<?php

use yii\db\Migration;

/**
 * Handles the creation of tables `{{%document_moving_of_goods}}`.
 */
class m190704_000006_create_document_moving_of_goods_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%document_moving_of_goods}}', [
            'id' => $this->primaryKey(),
            'is_deleted' => $this->boolean(),
            'is_posted' => $this->boolean(),
            'version' => $this->timestamp(),
            'date_time' => $this->timestamp()->notNull(),
            'number' => $this->string(12)->notNull(),
            'source_id' => $this->integer()->notNull(),
            'destination_id' => $this->integer()->notNull(),
            'author_id' =>  $this->integer()->notNull()
        ]);

        $this->createIndex(
            'document_moving_of_goods-source_id-index',
            'document_moving_of_goods',
            'source_id'
        );

        $this->addForeignKey(
            'document_moving_of_goods-source_id-fkey',
            'document_moving_of_goods',
            'source_id',
            'warehouse',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'document_moving_of_goods-destination_id-index',
            'document_moving_of_goods',
            'destination_id'
        );

        $this->addForeignKey(
            'document_moving_of_goods-destination_id-fkey',
            'document_moving_of_goods',
            'destination_id',
            'warehouse',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'document_moving_of_goods-author_id-fkey',
            'document_moving_of_goods',
            'author_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->createTable('{{%document_moving_of_goods_product}}', [
            'document_id' => $this->integer()->notNull(),
            'line_number' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' =>  $this->decimal(10, 3)->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'amount' =>  $this->decimal(10, 2)
        ]);

        $this->addPrimaryKey(
            'document_moving_of_goods_product-pkey',
            'document_moving_of_goods_product',
            [
                'document_id', 
                'line_number'
            ]
        );

        $this->createIndex(
            'document_moving_of_goods_product-document_id-index',
            'document_moving_of_goods_product',
            'document_id'
        );

        $this->addForeignKey(
            'document_moving_of_goods_product-document_id-fkey',
            'document_moving_of_goods_product',
            'document_id',
            'document_moving_of_goods',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'document_moving_of_goods_product-product_id-index',
            'document_moving_of_goods_product',
            'product_id'
        );

        $this->addForeignKey(
            'document_moving_of_goods_product-product_id-fkey',
            'document_moving_of_goods_product',
            'product_id',
            'product',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%document_moving_of_goods_product}}');
        $this->dropTable('{{%document_moving_of_goods}}');
    }
}
