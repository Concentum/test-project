<?php
namespace api\models\base;

use yii;
use app\models\base\ObjectProperty;
use app\models\base\PropertyValue;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;


class Proto extends \yii\db\ActiveRecord
{
    private $props = [];

    public function behaviors()
    {
        return [
            [
            'class' => TimestampBehavior::className(),
            'createdAtAttribute' => 'version',
            'updatedAtAttribute' => 'version',
            'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function extraFields() {
        return [
            'properties'
        ];
    }
  
    public function getProperties()
    {
        $condition = isset($this->id) ? ' AND object_id = ' . $this->id : ' AND object_id = null';
        return (new \yii\db\Query())
        ->select([
            'object_property.property_name as name', 'object_property.property_rules as rules', 
            'object_property.property_label as label', 'property_value.value as value', 'property_value.value as old_value',
            'property_value.property_id', 'property_value.id'
        ])->from('object_property')
        ->join('LEFT JOIN', 'property_value', 'object_property.id = property_value.property_id '. $condition)
        ->andWhere(['object_class' => $this->className()])->all();
    }

}