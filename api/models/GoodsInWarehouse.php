<?php
namespace api\models;

class GoodsInWarehouse extends \yii\db\ActiveRecord
{
    public $incoming;
    public $expense;
    public $begin_quantity;
    public $end_quantity;


    public static function tableName()
    {
        return 'goods_in_warehouse';
    }

    
    public function fields()
    {
        return [
            'warehouse',
            'product',
            'incoming' => function ($model) {
                return $model->incoming;
            }, 
            'expense' => function ($model) {
                return $model->expense;
            },
            'begin_quantity' => function ($model) {
                return $model->begin_quantity;
            },
            'end_quantity' => function ($model) {
                return $model->end_quantity;
            } 
 
        ];
    }
/*
    public function extraFields()
    {
        return [
            'turnover',
            'balance'
        ];
    }
*//*
    public function getTurnover()
    {   $query = 'select product_id, warehouse_id, 
                sum(CASE WHEN op = 1 THEN quantity END) as incoming, 
                sum(CASE WHEN op = 2 THEN quantity END) as expense 
                from goods_in_warehouse t1 
                group by product_id, warehouse_id';
        return $this->findBySql();
    } 

    public function getBalance()
    {   $query = '';
        return $this->findBySql($query);
    } 
*/
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    } 

    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    } 

} 