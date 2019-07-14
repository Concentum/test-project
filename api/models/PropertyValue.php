<?php
namespace api\models;

      
class PropertyValue extends \yii\db\ActiveRecord
{

    public static function tableName()
    {
        return 'property_value';
    }

    public function fields()
    {
        return ['id', 'value'];
    }

    public function rules()
    { 
        return  [
            [['period'], 'safe'],
            [['value'], 'string'],
            [['property_id', 'object_id'], 'integer'],
            [['property_id', 'object_id'], 'required'],
            [['property_id'], 'exist', 'skipOnError' => true, 'targetClass' => ObjectProperty::className(), 'targetAttribute' => ['property_id' => 'id']],
        ];
    }
} 