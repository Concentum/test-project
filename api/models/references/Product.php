<?php
namespace api\models\references;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "product".
 *
 *
 */
class Product extends \api\models\base\HierarchicalReference
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
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
            [['unit_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductUnit::className(), 'targetAttribute' => ['unit_id' => 'id']],
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
            'is_folder',
            'parent',
            'code',
            'description',
        //    'unit',
            'author'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge([
            'unit_id' => 'Unit',
        ], parent::attributeLabels());
    }

    /**
     * @inheritdoc
     */  
    public function extraFields()
    {
        return array_merge(parent::extraFields(), [
            'units' => function ($model) {
                return $model->productUnit;
            },
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUnit()
    {
        return $this->hasOne(ProductUnit::className(), ['id' => 'unit_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductUnit()
    {
        return $this->hasMany(ProductUnit::className(), ['product_id' => 'id']);
    }

}
