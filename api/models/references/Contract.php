<?php
namespace api\models\references;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "contract".
 *
 *
 */
class Contract extends \api\models\base\SimpleReference
{
    const CONTRACT_WITH_SUPPLIER = 1;
    const CONTRACT_WITH_BUYER = 2;
    const CONTRACT_OTHER = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contract';
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
            ['counterparty_id', 'integer'],
            ['counterparty_id', 'required'],
            [['date', 'expires_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
            ['number', 'string', 'max' => 12],
            ['counterparty_id', 'exist', 'skipOnError' => true, 'targetClass' => Counterparty::className(), 'targetAttribute' => ['counterparty_id' => 'id']],
            ['contract_type', 'in', 'range' => [self::CONTRACT_WITH_SUPPLIER, self::CONTRACT_WITH_BUYER, self::CONTRACT_OTHER]],
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
            'code',
            'description',
            'counterparty',
            'number',
            'date',
            'expires_at',
            'contract_type',
            'author'
        ];
    }

    /**
     * @inheritdoc
     */  
    public function extraFields()
    {
        return array_merge(parent::extraFields(), [
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'counterparty_id' => 'Counterparty',
            'number' => 'Number',
            'date' => 'Date',
            'expires_at' => 'Expires at',
            'contract_type' => 'Contract type',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounterparty()
    {
        return $this->hasOne(Counterparty::className(), ['id' => 'counterparty_id']);
    }

    public static function getContractTypes()
    {
        return [
            self::CONTRACT_WITH_SUPPLIER => \Yii::t('app', 'Contract with supplier'),
            self::CONTRACT_WITH_BUYER => \Yii::t('app', 'Contract with buyer'), 
            self::CONTRACT_OTHER => \Yii::t('app', 'Other contract'),
        ];
    }
}