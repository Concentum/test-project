<?php
namespace api\models\reports;

use api\models\references\Product;
use api\models\references\Warehouse;
use api\models\documents\ComingOfGoods;
use api\models\documents\ExpendOfGoods;

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
            'period',
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

    public function extraFields()
    {
        return [
            'recorder',
            'turnover',
            'balance'
        ];
    }

    public function getRecorder()
    {
        $classsName = $this->recorder_type == 1 ? ComingOfGoods::className() : ExpendOfGoods::className();
        return $this->hasOne($classsName, ['id' => 'recorder_id']);
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