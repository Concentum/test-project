<?php
namespace api\models\references;

/**
 * This is the model class for table "product_unit".
 *
 *
 */
class ProductUnit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product_unit';
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
          [['product_id', 'unit_id'], 'required'],
          [['product_id', 'unit_id'], 'integer'],
          ['ratio', 'number'],
          [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Product::className(), 'targetAttribute' => ['product_id' => 'id']],
          [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Unit::className(), 'targetAttribute' => ['unit_id' => 'id']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Product',
            'unit_id' => 'Unit',
            'ratio' => 'Ratio',
        ];
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), [
            'product',
            'unit',
            'ratio'
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(Unit::className(), ['id' => 'unit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }
}
  