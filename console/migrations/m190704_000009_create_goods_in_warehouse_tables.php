<?php

use yii\db\Migration;

/**
 * Handles the creation of tables `{{%goods_in_warehouse}}`.
 */
class m190704_000009_create_goods_in_warehouse_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%goods_in_warehouse}}', [
            'period' => $this->timestamp()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'warehouse_id' => $this->integer()->notNull(),
            'quantity' => $this->decimal(10, 3)->notNull(),
            'op' => $this->smallInteger()->notNull(),
            'recorder_id' => $this->integer()->notNull(),
            'recorder_type' => $this->smallInteger()->notNull()
        ]);

        $this->addPrimaryKey(
            'goods_in_warehouse-pkey',
            'goods_in_warehouse',
            [
                'period', 
                'product_id',
                'warehouse_id'
            ]
        );

        $this->addForeignKey(
            'goods_in_warehouse_product_id-fkey',
            'goods_in_warehouse',
            'product_id',
            'product',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'goods_in_warehouse_warehouse_id-fkey',
            'goods_in_warehouse',
            'warehouse_id',
            'warehouse',
            'id',
            'CASCADE'
        );

        $this->createTable('{{%goods_in_warehouse_total}}', [
            'period' => $this->timestamp()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'warehouse_id' => $this->integer()->notNull(),
            'quantity' => $this->decimal(10, 3)->notNull()
        ]);

        $this->addPrimaryKey(
            'goods_in_warehouse_total-pkey', 
            'goods_in_warehouse_total', 
            [
                'period',
                'product_id', 
                'warehouse_id'
            ]
        );

        $this->addForeignKey(
            'goods_in_warehouse_total-product_id-fkey',
            'goods_in_warehouse_total',
            'product_id',
            'product',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'goods_in_warehouse_total-warehouse_id-fkey',
            'goods_in_warehouse_total',
            'warehouse_id',
            'warehouse',
            'id',
            'CASCADE'
        );
              
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%goods_in_warehouse_total}}');

        $this->dropTable('{{%goods_in_warehouse}}');
    }
}
