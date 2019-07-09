<?php

namespace api\models\base;

use Yii;

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
class Document extends \yii\db\ActiveRecord
{
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    { 
        return [
            [['is_deleted', 'is_posted'], 'boolean'],
            [['version'], 'safe'],
            [['date_time'], 'safe'],
            [['number'], 'string', 'max' => 12],
            [['number', 'date_time'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_deleted' => 'Is Deleted',
            'is_posted' => 'Is Posted',
            'version' => 'Version',
            'date_time' => 'Date Time',
            'number' => 'Number',
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
            'is_posted'
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
}
