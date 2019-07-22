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
            CREATE FUNCTION public.recalc(tn text, period text) RETURNS void
                LANGUAGE plpgsql
                AS $$ DECLARE
                query_string text;
            BEGIN 
                query_string = \'
                DELETE FROM \'|| tn||\'_total WHERE \'|| tn||\'_total.period >= \'|| quote_literal(period::timestamp);
                EXECUTE query_string;
                query_string = \'
                INSERT INTO \'|| tn||\'_total(period, product_id, warehouse_id, quantity)
                    SELECT period, product_id, warehouse_id, SUM(quantity) OVER(PARTITION BY product_id, warehouse_id ORDER BY period) as quantity
                    FROM \'|| tn;
                EXECUTE query_string;
            END;$$;
        ');


       

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190704_110643_create_functions cannot be reverted.\n";

        $this->execute('DROP FUNCTION IF EXISTS public.recalc CASCADE;');

      //  return false;
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
