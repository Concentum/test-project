<?php
namespace api\models\documents;

use api\models\references\Product;
/**
 * This is the model class for table "document_coming_of_goods_product".
 *
 *
 */
class ComingOfGoodsProduct extends \api\models\base\DocumentDetail
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_coming_of_goods_product';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [

          [['product_id', 'quantity', 'price'], 'required'],
          [['product_id'], 'integer'],
          [['price', 'amount'], 'number', /*'numberPattern' => '/^(?=.*\d)\d*(?:\.\d{0,2})?$/' */],
          [['quantity'], 'number', /*'numberPattern' => '/^(?=.*\d)\d*(?:\.\d{0,3})?$/' */],
          [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Product',
            'quantity' => 'Quantity',
            'price' => 'Price',
            'amount' => 'Amount',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), [
            'product',
            'quantity',
            'price',
            'amount' => function () {
                return $this->price * $this->quantity;   
            }
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}
 