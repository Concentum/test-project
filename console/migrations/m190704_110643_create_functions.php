<?php

use yii\db\Migration;

/**
 * Class m190704_110643_create_functions
 */
class m190704_110643_create_functions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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

        END;
        $$;
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
        END;
        $$;
        ');


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
        END;
        $$;
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
        END;
        $$;
        ');

    
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

        $this->execute('
        CREATE FUNCTION public.recalc(tn text, period text) RETURNS void
            LANGUAGE plpgsql
            AS $$
        DECLARE
          query_string text;
        BEGIN 
          query_string = "DELETE FROM "|| tn||"_total WHERE "|| tn||"_total.period >= "|| quote_literal(period::timestamp);
          EXECUTE query_string;
          query_string = "INSERT INTO "|| tn||"_total(period, product_id, warehouse_id, quantity)
                          SELECT period, product_id, warehouse_id, SUM(quantity) OVER(PARTITION BY product_id, warehouse_id ORDER BY period) as quantity
                          FROM "|| tn;
          EXECUTE query_string;
        END;
        $$;
        ');


    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190704_110643_create_functions cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190704_110643_create_functions cannot be reverted.\n";

        return false;
    }
    */
}
