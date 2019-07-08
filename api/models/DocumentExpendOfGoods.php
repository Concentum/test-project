<?php
namespace api\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "document_expend_of_goods".
 *
 *
 */
class DocumentExpendOfGoods extends base\Document
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_expend_of_goods';
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
            'document_expend_of_goods_product' => function ($model) {
                return $model->documentExpendOfGoodsProduct;
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
        return $this->hasMany(DocumentExpendOfGoodsProduct::className(), ['document_id' => 'id']);
    }
}
