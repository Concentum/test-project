<?php
namespace api\models;

/**
 * This is the model class for table "document_coming_of_goods_product".
 *
 *
 */
class DocumentComingOfGoodsProduct extends base\DocumentDetail
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
          [['quantity', 'price'], 'number'],
          [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
        
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product' => 'Product',
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
          'amount'
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
 