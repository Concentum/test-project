<?php

use yii\db\Migration;

/**
 * Handles the creation of tables `{{%document_expend_of_goods}}`.
 */
class m190704_000008_create_document_expend_of_goods_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%document_expend_of_goods}}', [
            'id' => $this->primaryKey(),
            'is_deleted' => $this->boolean(),
            'is_posted' => $this->boolean(),
            'version' => $this->timestamp(),
            'date_time' => $this->timestamp()->notNull(),
            'number' => $this->string(12)->notNull(),
            'warehouse_id' => $this->integer()->notNull(),
            'counterparty_id' =>  $this->integer()->notNull(),
            'author_id' =>  $this->integer()->notNull()
        ]);

        $this->createIndex(
            'document_expend_of_goods-warehouse_id-index',
            'document_expend_of_goods',
            'warehouse_id'
        );

        $this->addForeignKey(
            'document_expend_of_goods-warehouse_id-fkey',
            'document_expend_of_goods',
            'warehouse_id',
            'warehouse',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'document_expend_of_goods-counterparty_id-index',
            'document_expend_of_goods',
            'counterparty_id'
        );

        $this->addForeignKey(
            'document_expend_of_goods-counterparty_id-fkey',
            'document_expend_of_goods',
            'counterparty_id',
            'counterparty',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'document_expend_of_goods-author_id-fkey',
            'document_expend_of_goods',
            'author_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->createTable('{{%document_expend_of_goods_product}}', [
            'document_id' => $this->integer()->notNull(),
            'line_number' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' =>  $this->decimal(10, 3)->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'amount' =>  $this->decimal(10, 2)
        ]);

        $this->addPrimaryKey(
            'document_expend_of_goods_product-pkey',
            'document_expend_of_goods_product',
            [
                'document_id', 
                'line_number'
            ]
        );

        $this->createIndex(
            'document_expend_of_goods_product-document_id-index',
            'document_expend_of_goods_product',
            'document_id'
        );

        $this->addForeignKey(
            'document_expend_of_goods_product-document_id-fkey',
            'document_expend_of_goods_product',
            'document_id',
            'document_expend_of_goods',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'document_expend_of_goods_product-product_id-index',
            'document_expend_of_goods_product',
            'product_id'
        );

        $this->addForeignKey(
            'document_expend_of_goods_product-product_id-fkey',
            'document_expend_of_goods_product',
            'product_id',
            'product',
            'id',
            'CASCADE'
        );

        $this->execute('
            CREATE FUNCTION public.document_expend_of_goods_tf() RETURNS trigger
                LANGUAGE plpgsql
                AS $$DECLARE
                query_string text;
                check_query_string text;
                result text; 
            BEGIN
                query_string = "INSERT INTO goods_in_warehouses(period, product_id, warehouse_id, quantity, op, recorder_id, recorder_type) 
                SELECT m.date, d.product_id, m.warehouse_id, -d.quantity as quantity, 2, m.id, 2
                FROM document_expend_of_goods m RIGHT JOIN document_expend_of_goods_detail d ON m.id = d.doc_id
                WHERE m.id="|| NEW.id; 

                check_query_string  = "select t1.product_id, t1.warehouse_id,  t1.period, t2.quantity - t3.quantity 
                from (
                select max(period) as period, product_id, warehouse_id
                from goods_in_warehouses_total
                where product_id in (select product_id from document_expend_of_goods_detail where doc_id = "|| NEW.id ||") 
                  and warehouse_id ="|| NEW.warehouse_id ||"
                group by product_id, warehouse_id
                ) t1 join goods_in_warehouses_total t2 on t1.period = t2.period and t1.product_id = t2.product_id and t1.warehouse_id = t2.warehouse_id
                join document_expend_of_goods_detail t3 on t1.product_id = t3.product_id
                where t2.quantity - t3.quantity < 0";

                IF TG_OP = "UPDATE" THEN
                    IF OLD.status = 1 AND NEW.status = 1 THEN
                        RAISE;
                    ELSIF OLD.status = 1 AND NEW.status = 0 THEN
                        DELETE FROM goods_in_warehouses where recorder_id = NEW.id AND recorder_type = 2;
                        RETURN NEW;
                    ELSIF OLD.status = 0 AND NEW.status = 1 THEN
                        EXECUTE check_query_string INTO result;
                        IF result IS  NOT NULL THEN 
                            RAISE;
                        END IF;
                        EXECUTE query_string;
                        RETURN NEW;
                    ELSE 
                        RETURN NEW;
                    END IF;  
                ELSIF TG_OP = "INSERT" THEN
                    IF NEW.is_posted = 1 THEN
                        RAISE;
                    ELSE 
                        RETURN NEW;
                    END IF;  
                ELSIF TG_OP = "DELETE" THEN
                    IF OLD.is_posted = 1 THEN
                        RAISE;
                    ELSE 
                        RETURN OLD;
                    END IF;
                END IF;
            END;$$;
        ');

        $this->execute('
            CREATE FUNCTION public.document_expend_of_goods_product_tf() RETURNS trigger
                LANGUAGE plpgsql
                AS $$DECLARE
                ROW RECORD;
            BEGIN
                ROW = CASE WHEN TG_OP = "DELETE" THEN OLD ELSE NEW END;
                IF (SELECT is_posted FROM document_expend_of_goods WHERE document_expend_of_goods.id = ROW.document_id) = true
                THEN 
                    RAISE;
                ELSE
                    RETURN ROW;
                END IF;
            END;$$;
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->execute('DROP FUNCTION IF EXISTS public.document_expend_of_goods_product_tf CASCADE;');
        $this->execute('DROP FUNCTION IF EXISTS public.document_expend_of_goods_tf CASCADE;');
        $this->dropTable('{{%document_expend_of_goods_product}}');
        $this->dropTable('{{%document_expend_of_goods}}');
    }
}
