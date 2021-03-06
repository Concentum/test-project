<?php
namespace api\models\registers;

use api\models\references\Product;
use api\models\references\Warehouse;
use api\models\documents\ComingOfGoods;
use api\models\documents\ExpendOfGoods;

class GoodsInWarehouse extends \yii\db\ActiveRecord
{
       
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
            'quantity',
            'recorder',
            'op'
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