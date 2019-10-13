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
            'unit_id' => $this->integer(),
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


        $this->createTable('{{%product_unit}}', [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer()->notNull(),
            'unit_id' => $this->integer()->notNull(),
            'ratio' => $this->decimal(10, 3),
        ]);

        $this->addForeignKey(
            'product_unit-product_id-fkey',
            'product_unit',
            'product_id',
            'product',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'product_unit-unit_id-fkey',
            'product_unit',
            'unit_id',
            'unit',
            'id',
            'CASCADE'
        );

//это ограничение относится к таблице product, но создаётся позже потому что ссылается на подчинённую таблицу product_unit
        /*
        $this->addForeignKey(
            'product-unit_id-fkey',
            'product',
            'unit_id',
            'product_unit',
            'id',
            'CASCADE'
        );*/

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'product_unit-product_id-fkey',
            'product_unit'
        );

        $this->dropForeignKey(
            'product_unit-unit_id-fkey',
            'product_unit'
        );

        $this->dropTable('{{%product_unit}}');

        
        $this->dropForeignKey(
            'product-parent_id-fkey',
            'product'
        );

    /*    $this->dropForeignKey(
            'product-unit_id-fkey',
            'product'
        ); */

        $this->dropForeignKey(
            'product-author_id-fkey',
            'product'
        );

        $this->dropIndex(
            'product-parent_id-index',
            'product'
        );
        
        $this->dropTable('{{%product}}');
    }
}
