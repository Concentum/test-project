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
                IF (TG_OP = \'UPDATE\') THEN
                   RAISE;
                ELSIF (TG_OP = \'INSERT\') THEN
                    SELECT quantity
                    FROM goods_in_warehouse_total
                    WHERE product_id = NEW.product_id AND warehouse_id = NEW.warehouse_id AND period <= NEW.period
                    ORDER BY period DESC
                    LIMIT 1 INTO _quantity;
                    /* Вставить новый итог */ 
                    INSERT INTO goods_in_warehouse_total(period, product_id, warehouse_id, quantity)
                    VALUES(NEW.period, NEW.product_id, NEW.warehouse_id, COALESCE(_quantity, 0) + NEW.quantity);
                    /* Пересчитать последующие итоги */ 
                    UPDATE goods_in_warehouse_total SET quantity = quantity + NEW.quantity WHERE product_id = NEW.product_id AND warehouse_id = NEW.warehouse_id AND period > NEW.period;
                    RETURN NEW;
                ELSIF (TG_OP = \'DELETE\') THEN
                    /* Удалить итог образованный текущим движением */
                    DELETE FROM goods_in_warehouse_total WHERE product_id = OLD.product_id AND warehouse_id = OLD.warehouse_id AND period = OLD.period;
                    /* Пересчитать последующие итоги */ 
                    UPDATE goods_in_warehouse_total SET quantity = quantity - OLD.quantity WHERE product_id = OLD.product_id AND warehouse_id = OLD.warehouse_id AND period >= OLD.period;
                    RETURN OLD;
                END IF;
            END;$$;
        ');

        $this->execute('
            CREATE TRIGGER goods_in_warehouse_trigger 
            BEFORE INSERT OR DELETE OR UPDATE ON public.goods_in_warehouse 
            FOR EACH ROW EXECUTE PROCEDURE public.goods_in_warehouse_tf();
        ');

        


/* *//*           
select date_trunc('second', t1.period) as period, t1.product_id, t1.warehouse_id, 
    coalesce(t2.quantity, 0) - sum(case when op = 1 then t1.quantity else 0 end) + sum(case when op = 2 then -t1.quantity else 0 end) begin_quantity,
    sum(case when op = 1 then t1.quantity else 0 end) as coming_quantity,
    sum(case when op = 2 then -t1.quantity else 0 end) as expend_quantity,
    coalesce(t2.quantity, 0) as end_quantity
from goods_in_warehouse t1 left join (
    select product_id, warehouse_id, period, quantity
    from goods_in_warehouse_total t1
    where period in (
        select max(period) from goods_in_warehouse_total
        where goods_in_warehouse_total.warehouse_id = t1.warehouse_id
            and goods_in_warehouse_total.product_id = t1.product_id
            and date_trunc('second', goods_in_warehouse_total.period) = date_trunc('second', t1.period)
    )
) t2 on t1.product_id = t2.product_id 
    and t1.warehouse_id = t2.warehouse_id 
    and date_trunc('second', t1.period) = date_trunc('second', t2.period)
group by date_trunc('second', t1.period), t1.product_id, t1.warehouse_id , coalesce(t2.quantity, 0) 
*/ 

        $this->execute('
        CREATE OR REPLACE FUNCTION goods_in_warehouse_remains_and_turnover(
            dateBegin TEXT DEFAULT null, 
            dateEnd TEXT DEFAULT null,
            product TEXT DEFAULT null,
            warehouse TEXT DEFAULT null,
            detailing TEXT DEFAULT \'millennium\'
        ) RETURNS TABLE (
            period timestamp, 
            product_id integer, 
            warehouse_id integer,
            begin_quantity numeric,
            coming_quantity numeric, 
            expend_quantity numeric,
            turnover numeric,
            end_quantity numeric
        )  
            LANGUAGE plpgsql
            AS $$ DECLARE
            query_string text;
            product_condition text;
            warehouse_condition text;
            period_condition text; 
        BEGIN
            period_condition = CASE WHEN dateBegin IS NULL AND dateEnd IS NULL THEN  \'\'
                                WHEN dateBegin IS NOT NULL AND dateEnd IS NULL THEN \' and  t1.period >= \'|| quote_literal(dateBegin::timestamp)
                                WHEN dateBegin IS NULL AND dateEnd IS NOT NULL THEN \' and  t1.period <= \'|| quote_literal(dateEnd::timestamp)
                                ELSE  \' and  t1.period between \'|| quote_literal(dateBegin::timestamp) ||\' and \'|| quote_literal(dateEnd::timestamp)
                             END;

            product_condition = CASE WHEN product IS NULL THEN \'\' ELSE \' AND \'|| replace(product, \'product\', \'t1.product\') END;
            warehouse_condition = CASE WHEN warehouse IS NULL THEN \'\' ELSE \' AND \'|| replace(warehouse, \'warehouse\', \'t1.warehouse\') END; 

            detailing = quote_literal(detailing);
            
            query_string = \'select date_trunc(\'||detailing||\', t1.period), t1.product_id, t1.warehouse_id, 
                coalesce(t2.quantity, 0) - sum(case when op = 1 then t1.quantity else 0 end) + sum(case when op = 2 then -t1.quantity else 0 end) begin_quantity,
                sum(case when op = 1 then t1.quantity else 0 end) as coming_quantity,
                sum(case when op = 2 then -t1.quantity else 0 end) as expend_quantity,
                sum(case when op = 1 then t1.quantity else 0 end) - sum(case when op = 2 then -t1.quantity else 0 end) as turnover,
                coalesce(t2.quantity, 0) as end_quantity
            from goods_in_warehouse t1
            left join (
                select product_id, warehouse_id, period, quantity
                from goods_in_warehouse_total t1
                where period in (
                    select max(period) from goods_in_warehouse_total
                    where date_trunc(\'||detailing||\', t1.period) = date_trunc(\'||detailing||\', period) \'|| period_condition || product_condition || warehouse_condition ||\'
                    group by date_trunc(\'||detailing||\', t1.period), product_id, warehouse_id
                ) 
            ) t2 on t1.product_id = t2.product_id and t1.warehouse_id = t2.warehouse_id and date_trunc(\'||detailing||\', t1.period) = date_trunc(\'||detailing||\', t2.period)
            where 0=0 \'|| period_condition || product_condition || warehouse_condition ||\'
            group by  date_trunc(\'||detailing||\', t1.period), t1.product_id, t1.warehouse_id, coalesce(t2.quantity, 0)\'; 

            RETURN QUERY EXECUTE query_string;
        END;$$;
        ');

/*Другой вариант функции, с подготовленным условием для where*/
        $this->execute('
        CREATE OR REPLACE FUNCTION goods_in_warehouse_remains_and_turnover(
            _condition TEXT DEFAULT null, 
            detailing TEXT DEFAULT \'millennium\'
        ) RETURNS TABLE (
            period timestamp, 
            product_id integer, 
            warehouse_id integer,
            begin_quantity numeric,
            coming_quantity numeric, 
            expend_quantity numeric,
            turnover numeric,
            end_quantity numeric
        )  
            LANGUAGE plpgsql
            AS $$ DECLARE
            query_string text;
            where_condition text; 
        BEGIN
            where_condition =  replace(where_condition, \'product\', \'t1.product\');
            where_condition =  replace(where_condition, \'warehouse\', \'t1.warehouse\');
            where_condition =  replace(where_condition, \'period\', \'t1.period\');
            where_condition =  \' AND \'|| where_condition;
            detailing = quote_literal(detailing);
            
            query_string = \'select date_trunc(\'||detailing||\', t1.period), t1.product_id, t1.warehouse_id, 
                coalesce(t2.quantity, 0) - sum(case when op = 1 then t1.quantity else 0 end) + sum(case when op = 2 then -t1.quantity else 0 end) begin_quantity,
                sum(case when op = 1 then t1.quantity else 0 end) as coming_quantity,
                sum(case when op = 2 then -t1.quantity else 0 end) as expend_quantity,
                sum(case when op = 1 then t1.quantity else 0 end) - sum(case when op = 2 then -t1.quantity else 0 end) as turnover,
                coalesce(t2.quantity, 0) as end_quantity
            from goods_in_warehouse t1
            left join (
                select product_id, warehouse_id, period, quantity
                from goods_in_warehouse_total t1
                where period in (
                    select max(period) from goods_in_warehouse_total
                    where date_trunc(\'||detailing||\', t1.period) = date_trunc(\'||detailing||\', period) \'|| where_condition ||\'
                    group by date_trunc(\'||detailing||\', t1.period), product_id, warehouse_id
                ) 
            ) t2 on t1.product_id = t2.product_id and t1.warehouse_id = t2.warehouse_id and date_trunc(\'||detailing||\', t1.period) = date_trunc(\'||detailing||\', t2.period)
            where 0=0 \'|| where_condition ||\'
            group by  date_trunc(\'||detailing||\', t1.period), t1.product_id, t1.warehouse_id, coalesce(t2.quantity, 0)\'; 

            RETURN QUERY EXECUTE query_string;
        END;$$;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute('DROP FUNCTION IF EXISTS public.goods_in_warehouse_remains_and_turnover CASCADE;');
        $this->execute('DROP FUNCTION IF EXISTS public.goods_in_warehouse_tf CASCADE;');
        $this->execute('DROP TRIGGER IF EXISTS goods_in_warehouse_trigger ON goods_in_warehouse;');
     
        $this->dropTable('{{%goods_in_warehouse_total}}');
        $this->dropTable('{{%goods_in_warehouse}}');
    }
}
