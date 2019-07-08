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

  
  public function getReferenceAttributes() 
  {
    $result = [];
    foreach($this->getValidators() as $key => $value) {
        if (get_class($value) ===  'yii\validators\ExistValidator') {
            $tmp = explode('\\', $value->targetClass);
            $tmp = array_pop($tmp);
            $url = [strtolower($tmp) . '/list'];
            if ($value->attributes[0] == 'parent_id') {
                $url['folder_only'] = true;
            }
            $result[$value->attributes[0]] = [
                'targetClass' => $value->targetClass, 
                'url' => \yii\helpers\Url::to($url)  
            ];
        }
    }
    return $result;
  }

  public function getProperties()
  {
    return ObjectProperty::find()->where(['object_class' => $this->getShortClassName()]);
  }

  public function getPropertyValues()
  {
    $condition = isset($this->id) ? ' AND object_id = ' . $this->id : ' AND object_id = null';
    return (new \yii\db\Query())
    ->select(['object_property.property_name', 'object_property.property_rules',  'object_property.property_label', 'property_value.value'])
    ->from('object_property')
    ->join('LEFT JOIN', 'property_value', 'object_property.id = property_value.property_id '. $condition)
    ->andWhere(['object_class' => $this->getShortClassName()]);
  }

  public function getShortClassName()
  {
    return array_slice(explode('\\', $this->ClassName()), -1);
  }

  public function afterSave($insert, $changedAttributes)
  {
    foreach($this->props as $key => $val) {
        if (empty($val)) continue;
        
      //  var_dump($this->props[$key]['property_name']);exit;

        $op = ObjectProperty::find()->where(['property_name' => $this->props[$key]['property_name']])->one();
        $pv = PropertyValue::findOne([
          'property_id' => $op->id,
          'object_id' => $this->id 
        ]);
        if (!$pv) {
          $pv = new PropertyValue();
          $pv->property_id = $op->id;
          $pv->object_id = $this->id;
        }
        $pv->value = $this->props[$key]['value'];
        $pv->save();
    }
  }

  public function afterDelete()
  {
      
  }

  public function rules()
  { 
    $this->props = $this->getPropertyValues()->all();
    $rules = [];
    foreach ($this->props as $key => $val) {
      $rules = array_merge($rules, json_decode($val['property_rules'], true));
    }  
    return $rules;  
  }

  public function attributeLabels()
  {
      return ArrayHelper::map($this->props, 'property_name', 'property_label');
  }


  public function __get($name)
  {
    if (!$this->hasAttribute($name) && /* $name !== 'dirtyAttributes' && */ !method_exists($this, 'get'.$name)) {
        $value = ArrayHelper::map($this->props, 'property_name', 'value')[$name];
    } else {
      $value = parent::__get($name);
    }
    return $value;  
  } 
  
  public function __set($name, $value)
  { 
    if (!$this->hasAttribute($name)) {
      foreach($this->props as $key => $val) {
        if ($val['property_name'] == $name) {
          $this->props[$key]['value'] = $value;     
        } 
      }
    } else {
      parent::__set($name, $value);
    }
  }

}