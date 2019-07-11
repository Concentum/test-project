<?php
namespace api\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "document_coming_of_goods".
 *
 *
 */
class DocumentComingOfGoods extends base\Document
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_coming_of_goods';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'author_id',
                'updatedByAttribute' => false,
            ],
        ]);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['counterparty_id', 'warehouse_id'], 'integer'],
            [['counterparty_id', 'warehouse_id'], 'required'],
            [['counterparty_id'], 'exist', 'skipOnError' => true, 'targetClass' => Counterparty::className(), 'targetAttribute' => ['counterparty_id' => 'id']],
            [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'warehouse_id' => 'Warehouse',
            'counterparty_id' => 'Counterparty',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    { 
        return array_merge(parent::fields(), [
            'counterparty',
            'warehouse',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return array_merge(parent::fields(), [ 
            'document_coming_of_goods_product' => function ($model) {
                return $model->documentComingOfGoodsProduct;
            }, 
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounterparty()
    {
        return $this->hasOne(Counterparty::className(), ['id' => 'counterparty_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentComingOfGoodsProduct()
    {
        return $this->hasMany(DocumentComingOfGoodsProduct::className(), ['document_id' => 'id']);
    }

    public static function details()
    {
        return [
            'products' => DocumentComingOfGoodsProduct::className() 
        ];
    }
}
