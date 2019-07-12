<?php
namespace api\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "document_moving_of_goods".
 *
 *
 */
class DocumentMovingOfGoods extends base\Document
{
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_moving_of_goods';
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
            [['source_id', 'destination_id'], 'integer'],
            [['source_id', 'destination_id'], 'required'],
            [['source_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['source_id' => 'id']],
            [['destination_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['destination_id' => 'id']],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'source_id' => 'Source',
            'destination_id' => 'Destination',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return [
            'id',
            'is_deleted',
            'is_posted',
            'number',
            'date_time',
            'source',
            'destination',
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return array_merge(parent::fields(), [ 
            'products' => function ($model) {
                return $model->documentMovingOfGoodsProduct;
            },
            'author' 
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'source_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDestination()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'destination_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentMovingOfGoodsProduct()
    {
        return $this->hasMany(DocumentMovingOfGoodsProduct::className(), ['document_id' => 'id']);
    }

    public static function getDetails()
    {
        return [
            'products' => DocumentMovingOfGoodsProduct::className() 
        ];
    }
}
