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
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'author_id',
                'updatedByAttribute' => false,
            ],
        ]);;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'source' => 'Source',
            'destination' => 'Destination',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        return array_merge(parent::fields(), [
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

    public static function details()
    {
        return [
            'products' => DocumentMovingOfGoodsProduct::className() 
        ];
    }
}
