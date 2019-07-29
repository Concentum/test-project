<?php
namespace api\models;

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
            [['property_rules'], 'safe'],
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