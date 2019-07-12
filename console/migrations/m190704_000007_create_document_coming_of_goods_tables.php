<?php

use yii\db\Migration;

/**
 * Handles the creation of tables `{{%document_coming_of_goods}}`.
 */
class m190704_000007_create_document_coming_of_goods_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%document_coming_of_goods}}', [
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
            'document_coming_of_goods-warehouse_id_index',
            'document_coming_of_goods',
            'warehouse_id'
        );

        $this->addForeignKey(
            'document_coming_of_goods-warehouse_id-fkey',
            'document_coming_of_goods',
            'warehouse_id',
            'warehouse',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'document_coming_of_goods-counterparty_id-index',
            'document_coming_of_goods',
            'counterparty_id'
        );

        $this->addForeignKey(
            'document_coming_of_goods-counterparty_id-fkey',
            'document_coming_of_goods',
            'counterparty_id',
            'counterparty',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'document_coming_of_goods-author_id-fkey',
            'document_coming_of_goods',
            'author_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->createTable('{{%document_coming_of_goods_product}}', [
            'document_id' => $this->integer()->notNull(),
            'line_number' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'quantity' =>  $this->decimal(10, 3)->notNull(),
            'price' => $this->decimal(10, 2)->notNull(),
            'amount' =>  $this->decimal(10, 2)
        ]);

        $this->addPrimaryKey(
            'document_coming_of_goods_product-pkey',
            'document_coming_of_goods_product',
            [
                'document_id', 
                'line_number'
            ]
        );

        $this->createIndex(
            'document_coming_of_goods_product-document_id-index',
            'document_coming_of_goods_product',
            'document_id'
        );

        $this->addForeignKey(
            'document_coming_of_goods_product-document_id-fkey',
            'document_coming_of_goods_product',
            'document_id',
            'document_coming_of_goods',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'document_coming_of_goods_product-product_id-index',
            'document_coming_of_goods_product',
            'product_id'
        );

        $this->addForeignKey(
            'document_coming_of_goods_product-product_id-fkey',
            'document_coming_of_goods_product',
            'product_id',
            'product',
            'id',
            'CASCADE'
        );

        $this->execute('
            CREATE FUNCTION public.document_coming_of_goods_tf() RETURNS trigger
                LANGUAGE plpgsql
                AS $$DECLARE
                query_string text;
            BEGIN
                IF TG_OP <> "DELETE" THEN 
                    query_string = "INSERT INTO goods_in_warehouse(period, product_id, warehouse_id, quantity, op, recorder_id, recorder_type) 
                    SELECT m.date, d.product_id, m.warehouse_id, d.quantity, 1, m.id, 1
                    FROM document_coming_of_goods m RIGHT JOIN document_coming_of_goods_product d ON m.id = d.doc_id
                    WHERE m.id="|| NEW.id;    
                END IF;
                IF TG_OP = "UPDATE" THEN
                    IF OLD.is_posted = true AND NEW.is_posted = true THEN
                        RAISE;
                    ELSIF OLD.is_posted = true AND NEW.is_posted = false THEN
                        DELETE FROM goods_in_warehouses where recorder_id = NEW.id AND recorder_type = 1;
                        RETURN NEW;
                    ELSIF OLD.is_posted = false AND NEW.is_posted = true THEN
                        EXECUTE query_string;
                        RETURN NEW;
                    ELSIF OLD.is_posted = false AND NEW.is_posted = false THEN
                        RETURN NEW;
                    END IF;  
                ELSIF TG_OP = "INSERT" THEN
                    IF NEW.is_posted = true THEN
                        RAISE;
                    ELSE 
                        RETURN NEW;
                    END IF;  
                ELSIF TG_OP = "DELETE" THEN
                    IF OLD.is_posted = true THEN
                        RAISE;
                    ELSE 
                        RETURN OLD;
                    END IF;
                END IF;
            END;$$;
        ');

        $this->execute('
            CREATE FUNCTION public.document_coming_of_goods_product_tf() RETURNS trigger
                LANGUAGE plpgsql
                AS $$DECLARE
                ROW RECORD;
            BEGIN
                ROW = CASE WHEN TG_OP = "DELETE" THEN OLD ELSE NEW END;
                IF (SELECT is_posted FROM document_coming_of_goods WHERE document_coming_of_goods.id = ROW.document_id) = true
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
        $this->execute('DROP FUNCTION IF EXISTS public.document_coming_of_goods_product_tf CASCADE;');
        $this->execute('DROP FUNCTION IF EXISTS public.document_coming_of_goods_tf CASCADE;');
        
        $this->dropTable('{{%document_coming_of_goods_product}}');
        $this->dropTable('{{%document_coming_of_goods}}');
    }
}
