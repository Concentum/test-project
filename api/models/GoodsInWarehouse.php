<?php
namespace api\models;

class GoodsInWarehouse extends \yii\db\ActiveRecord
{
    public $coming;
    public $expend;
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
            'coming' => function ($model) {
                return $model->coming;
            }, 
            'expend' => function ($model) {
                return $model->expend;
            },
            'begin_quantity' => function ($model) {
                return $model->begin_quantity;
            },
            'end_quantity' => function ($model) {
                return $model->end_quantity;
            } 
 
        ];
    }

    public function extraFields()
    {
        return [
            'turnover',
            'balance'
        ];
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    } 

    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    } 

} 