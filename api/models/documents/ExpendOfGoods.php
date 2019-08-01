<?php
namespace api\models\documents;

use api\models\references\Counterparty;
use api\models\references\Warehouse;

/**
 * This is the model class for table "document_expend_of_goods".
 *
 *
 */
class ExpendOfGoods extends \api\models\base\Document
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
            'author_id' => 'Author'
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
            'counterparty',
            'warehouse',
            'author'
        ];
    }

    /**
     * @inheritdoc
     */
    public function extraFields()
    {
        return array_merge(parent::fields(), [ 
            'products' => function ($model) {
                return $model->expendOfGoodsProduct;
            },
            'author' 
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
    public function getExpendOfGoodsProduct()
    {
        return $this->hasMany(ExpendOfGoodsProduct::className(), ['document_id' => 'id']);
    }

    public static function getDetails()
    {
        return [
            'products' => ExpendOfGoodsProduct::className() 
        ];
    }
}
