<?php
namespace api\models;

use yii\helpers\Inflector;
      
class PropertyValue extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'property_value';
    }

    public function fields()
    {
        return [
            'id',
            'object',
            'objectRepresentation',
            'property',
            'propertyRepresentation',
            'value',
        ];
    }

    public function rules()
    { 
        return  [
            [['period'], 'safe'],
            [['value'], 'string'],
            [['property_id', 'object_id'], 'integer'],
            [['property_id', 'object_id'], 'required'],
            [['object_id'], 'exist', 'skipOnError' => true, /*'targetClass' => '',*/ 'targetAttribute' => ['object_id' => 'id']],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectProperty::className(), 'targetAttribute' => ['property_id' => 'id']],
        ];
    }

    public function getProperty()
    {
        return $this->hasOne(ObjectProperty::className(), ['id' => 'property_id']);
    }

    public function getObject()
    { 
        return $this->hasOne($this->property->object_class, ['id' => 'object_id']);
    } 

    public function getObjectRepresentation()
    { 
        return $this->object->representation;
    } 

    public function getPropertyRepresentation()
    { 
        return $this->property->representation;
    } 

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'object_id' => 'Object',
            'property_id' => 'Property',
            'value' => 'Value',
        ];
    }

    public function title($plural = true)
    {   
        return Inflector::camel2words($plural ? Inflector::pluralize(basename(get_class($this))) : basename(get_class($this)));
    }
} 