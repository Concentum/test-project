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
           
        $this->execute('
        CREATE FUNCTION public.goods_in_warehouse_tf() RETURNS trigger
            LANGUAGE plpgsql
            AS $$DECLARE
            _quantity numeric;
        BEGIN
          IF (TG_OP = "UPDATE") THEN
             RAISE;
          ELSIF (TG_OP = "INSERT") THEN
             SELECT quantity
             FROM goods_in_warehouses_total
             WHERE product_id = NEW.product_id AND warehouse_id = NEW.warehouse_id AND period <= NEW.period
             ORDER BY period DESC
             LIMIT 1 INTO _quantity;
             /* Вставить новый итог */ 
             INSERT INTO goods_in_warehouse_total(period, product_id, warehouse_id, quantity)
             VALUES(NEW.period, NEW.product_id, NEW.warehouse_id, COALESCE(_quantity, 0) + NEW.quantity);
             /* Пересчитать последующие итоги */ 
             UPDATE goods_in_warehouse_total SET quantity = quantity + NEW.quantity WHERE product_id = NEW.product_id AND warehouse_id = NEW.warehouse_id AND period > NEW.period;
             RETURN NEW;
          ELSIF (TG_OP = "DELETE") THEN
             /* Удалить итог образованный текущим движением */
             DELETE FROM goods_in_warehouse_total WHERE product_id = OLD.product_id AND warehouse_id = OLD.warehouse_id AND period = OLD.period;
             /* Пересчитать последующие итоги */ 
             UPDATE goods_in_warehouse_total SET quantity = quantity - OLD.quantity WHERE product_id = OLD.product_id AND warehouse_id = OLD.warehouse_id AND period >= OLD.period;
             RETURN OLD;
          END IF;
        END;
        $$;
        ');
   
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP FUNCTION IF EXISTS public.good_in_warehouse_tf CASCADE;');
        $this->dropTable('{{%goods_in_warehouse_total}}');
        $this->dropTable('{{%goods_in_warehouse}}');
    }
}
