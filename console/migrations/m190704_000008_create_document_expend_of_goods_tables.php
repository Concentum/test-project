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
                query_string = \'
                INSERT INTO goods_in_warehouse(period, product_id, warehouse_id, quantity, op, recorder_id, recorder_type) 
                    SELECT m.date_time, d.product_id, m.warehouse_id, SUM(-d.quantity) as quantity, 2, m.id, 2
                    FROM document_expend_of_goods m RIGHT JOIN document_expend_of_goods_product d ON m.id = d.document_id
                    WHERE m.id=\'|| NEW.id ||\' 
                    GROUP BY m.date_time, d.product_id, m.warehouse_id, 2, m.id, 2 \'; 

                check_query_string  = \'
                SELECT t1.product_id, t1.warehouse_id, t1.period, t2.quantity - t3.quantity 
                FROM (
                    SELECT MAX(period) AS period, product_id, warehouse_id
                        FROM goods_in_warehouse_total
                        WHERE product_id in (
                            SELECT product_id 
                            FROM document_expend_of_goods_product WHERE document_id = \'|| NEW.id ||\') 
                            AND warehouse_id =\'|| NEW.warehouse_id ||\'
                            GROUP BY product_id, warehouse_id
                    ) t1 JOIN goods_in_warehouse_total t2 ON t1.period = t2.period AND t1.product_id = t2.product_id                                                           AND t1.warehouse_id = t2.warehouse_id
                JOIN document_expend_of_goods_product t3 ON t1.product_id = t3.product_id
                WHERE t2.quantity - t3.quantity < 0\';

                IF TG_OP = \'UPDATE\' THEN
                    IF OLD.is_posted = true AND NEW.is_posted = true THEN
                        RAISE;
                    ELSIF OLD.is_posted = true AND NEW.is_posted = false THEN
                        DELETE FROM goods_in_warehouse where recorder_id = NEW.id AND recorder_type = 2;
                        RETURN NEW;
                    ELSIF OLD.is_posted = false AND NEW.is_posted = true THEN
                        EXECUTE check_query_string INTO result;
                        IF result IS  NOT NULL THEN 
                            RAISE;
                        END IF;
                        EXECUTE query_string;
                        RETURN NEW;
                    ELSE 
                        RETURN NEW;
                    END IF;  
                ELSIF TG_OP = \'INSERT\' THEN
                    IF NEW.is_posted = true THEN
                        RAISE ;
                    ELSE 
                        RETURN NEW;
                    END IF;  
                ELSIF TG_OP = \'DELETE\' THEN
                    IF OLD.is_posted = true THEN
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
                ROW = CASE WHEN TG_OP = \'DELETE\' THEN OLD ELSE NEW END;
                IF (SELECT is_posted FROM document_expend_of_goods WHERE document_expend_of_goods.id = ROW.document_id) = true
                THEN 
                    RAISE;
                ELSE
                    RETURN ROW;
                END IF;
            END;$$;
        ');

        $this->execute('
            CREATE TRIGGER document_expend_of_goods_product_trigger 
            BEFORE INSERT OR DELETE OR UPDATE ON public.document_expend_of_goods_product
            FOR EACH ROW EXECUTE PROCEDURE public.document_expend_of_goods_product_tf();
        ');

        $this->execute('
            CREATE TRIGGER document_expend_of_goods_trigger 
            BEFORE INSERT OR DELETE OR UPDATE ON public.document_expend_of_goods 
            FOR EACH ROW EXECUTE PROCEDURE public.document_expend_of_goods_tf();
        ');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {   
        $this->execute('DROP TRIGGER IF EXISTS document_expend_of_goods_product_trigger;');
        $this->execute('DROP TRIGGER IF EXISTS document_expend_of_goods_trigger;');
        
        $this->execute('DROP FUNCTION IF EXISTS public.document_expend_of_goods_product_tf CASCADE;');
        $this->execute('DROP FUNCTION IF EXISTS public.document_expend_of_goods_tf CASCADE;');
        
        $this->dropTable('{{%document_expend_of_goods_product}}');
        $this->dropTable('{{%document_expend_of_goods}}');
    }
}
