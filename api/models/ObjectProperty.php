<?php
namespace api\models;

use yii\helpers\Inflector;
use api\models\PropertyValue;

class ObjectProperty extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'object_property';
    }

    public function rules()
    { 
        return  [
            [['object_class', 'property_name', 'property_label'], 'string', 'max' => 64],
            [['property_rules'], 'string', 'max' => 256],
            [['is_periodic'], 'boolean'],
            [['object_class', 'property_name', 'property_label', 'property_rules'], 'required']
        ];
    }

    public function fields()
    {
        return [
            'id',
            'object_class',
            'property_name',
            'property_label',
        ];
    }

    public function extraFields()
    {
        return [
            'rules' => 'property_rules',
        ];
    }

    public function getPropertyValues()
    {
        return $this->hasMany(PropertyValue::className(), ['property_id' => 'id']);
    } 

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'object_class' => 'Object class',
            'property_name' => 'Property name',
            'property_label' => 'Label',
            'property_rules' => 'Rules'
        ];
    }

    public function getRepresentation()
    {
       return $this->property_name;
    }

    public function mainRepresentation()
    {
        return ['property_label'];
    }

    public function title($plural = true)
    {   
        return Inflector::camel2words($plural ? Inflector::pluralize(basename(get_class($this))) : basename(get_class($this)));
    }
} 

/* note 
/* правила небходимо указывать в json
/* пример:

    [
        "value",
        "string",
        {
            "min": 5,
            "max": 10
        }
    ]

ещё

    [
        "value",
        "exist",
        {
            "targetClass": "api\\models\\Counterparty",
            "targetAttribute": {
                "value": "id"
            }
        }
    ]

*/