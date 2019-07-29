<?php

namespace api\models\base;

use Yii;
use api\models\User;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "document_coming_of_goods".
 *
 * @property int $id
 * @property bool $is_deleted
 * @property bool $is_posted
 * @property string $version
 * @property string $date_time
 * @property string $number
 * @property int $warehouse_id
 * @property int $counterparty_id
 *
 * @property Counterparty $counterparty
 * @property Warehouse $warehouse
 * @property DocumentComingOfGoodsProducts[] $documentComingOfGoodsProducts
 */
class Document extends Proto
{
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
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    { 
        return [
            [['number', 'date_time'], 'required'],
            [['is_deleted', 'is_posted'], 'boolean'],
        //    [['version'], 'safe'],
            [['date_time'], 'datetime'],
            [['number'], 'string', 'max' => 12],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
    //        'id' => 'ID',
    //        'is_deleted' => 'Is Deleted',
    //        'is_posted' => 'Is Posted',
        'number' => 'Number',
        'date_time' => 'Date Time',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function fields()
    {
        return [
            'id',
            'number',
            'date_time',
            'version',
            'is_deleted',
            'is_posted',
        ];
    } 

    /**
     * {@inheritdoc}
     */
    public function extraFields()
    {
        return [
            'author'
        ];
    }

    public function getReference($className)
    {
        return $this->hasOne($className, ['id' => $className.'_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetail($className)
    {
        return $this->hasMany($className, ['document_id' => 'id']);
    }

    public function getRepresentation()
    {
       return $this->number.' оf '.$this->date_time;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }
}
