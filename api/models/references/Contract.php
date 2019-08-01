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
            [['date', 'expires_at'], 'datetime'],
            ['number', 'string', 'max' => 12],
            ['counterparty_id', 'exist', 'skipOnError' => true, 'targetClass' => Counterparty::className(), 'targetAttribute' => ['counterparty_id' => 'id']],
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
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCounterparty()
    {
        return $this->hasOne(Counterparty::className(), ['id' => 'counterparty_id']);
    }
}