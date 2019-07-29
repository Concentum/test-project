<?php
namespace api\controllers;

use Yii;
use yii\rest\Controller;
use yii\helpers\Inflector;

/**
 * Metadata controller
 */
class MetadataController extends Controller
{
    private $nspace = 'api\models';
    private $entitys = [
        'references' => ['Product', 'Counterparty', 'Warehouse'],
        'documents' => ['DocumentComingOfGoods', 'DocumentExpendOfGoods', 'DocumentMovingOfGoods'], 
    ];
    private $other = [
        "interfaces" => [
            "main" => [
                "menu" => [
                    ["label" => "Документы", "items" => [
                        ["label" => "Приход товара", "endpoint" => "documents.document-coming-of-goods"],
                        ["label" => "Расход товара", "endpoint" => "documents.document-expend-of-goods"],
                        ["label" => "Перемещение товара", "endpoint" => "documents.document-moving-of-goods"]
                    ]],
                    ["label" => "Справочники", "items" => [
                        ["label" => "Товары", "endpoint" => "references.products"],
                        ["label" => "Склады", "endpoint" => "references.warehouses"],
                        ["label" => "Контрагенты", "endpoint" => "references.counterparties"],
                        ["label" => "Договоры", "endpoint" => "references.contracts"]
                    ]] 
                ]
            ]
        ]	
    ];

    /**
    * {@inheritdoc}
    */
    public function behaviors()
    {
        return [
            'corsFilter' => [
              'class' => \yii\filters\Cors::className(),
            ],
            /*      'authenticator' => [
            'class' => \yii\filters\auth\HttpBearerAuth::className(),
            ], */
        ];
    }
    

    function getValidationRules($attribute, $validator) 
    {   
        $result = [];
    //    Yii::info(get_class($validator));
        
        switch (get_class($validator)) {
            case 'yii\validators\ExistValidator':
                $result['type'] = 'link';
                $result['target'] = Inflector::camel2id(Inflector::pluralize(basename($validator->targetClass)));
                if (isset($validator->filter))
                    $result['filter'] = $validator->filter;
                break;
            case 'yii\validators\BooleanValidator':
                $result['type'] = 'boolean';
                break;
            case 'yii\validators\StringValidator':
                $result['type'] = 'string';
                break;
            case 'yii\validators\IntegerValidator':
                $result['type'] = 'integer';
                break;
            case 'yii\validators\NumberValidator':
                $result['type'] = 'number';
                break;
            case 'yii\validators\DateValidator':
                $result['type'] = 'date';
                if (isset($validator->type))
                    $result['structure'] = $validator->type;
                break;
            case 'yii\validators\RequiredValidator':    
                $result['required'] = true;
                break;
            case 'yii\validators\DefaulrValueValidator':    
                $result['default'] = $validator->value;
        }
        if (isset($validator->min))
            $result['min'] =  $validator->min;
        if (isset($validator->max))    
            $result['max'] =  $validator->max;
        if (isset($validator->length))
            $result['length'] = $validator->length;
        if (isset($validator->format))
            $result['format'] =  $validator->format;
        
        return $result;
    }

    public function actionIndex()
    {   
        $md2 = [];
        $properties = \api\models\ObjectProperty::find()->asArray()->all();
        $excludeValidators = ['enableClientValidation', 'forceMasterDb', 'targetAttributeJunction'];
        foreach($this->entitys as $key => $items) {
            foreach($items as $entity) {
                $modelClass = $this->nspace.'\\'.$entity;
                $model = new $modelClass();
                $transformEntity = Inflector::camel2id(Inflector::pluralize($entity));
                $md2[$key][ $transformEntity ]['title'] = $model->title();

                $classProperties = array_filter($properties, function($var) use($modelClass) {
                    return $var['object_class'] == $modelClass;
                });
                foreach($classProperties as $v) {
                    $dm = new \yii\base\DynamicModel(['value' => null]);
                    if (isset($v['property_rules']) && is_array(json_decode($v['property_rules']))) {
                        $rule = json_decode($v['property_rules'], true);
                        list($a, $b, $c) = $rule;
                        $dm->addRule($a, $b, $c);
                    }   
                    $rules = [];
                    foreach($dm->getValidators() as $validator) {
                        $rules = $this->getValidationRules('value', $validator);
                        \yii::info($rules);
                    } 
                    $md2[$key][$transformEntity]['properties'][$v['property_name']] = array_merge(['label' => $v['property_label']], $rules);
                }
            

                $labels = $model->attributeLabels();
                foreach($model->attributes() as $attr) {
                    if (isset($labels[$attr]))
                        $md2[$key][$transformEntity]['attributes'][$attr]['label'] = $labels[$attr]; 
                } 
                
                foreach($model->getValidators() as $validator) {
                    $rules = [];
                    foreach($validator->attributes as $attribute) {
                        $rules = $this->getValidationRules($attribute, $validator);
                        if (isset($md2[$key][$transformEntity]['attributes'][$attribute]))
                        $md2[$key][$transformEntity]['attributes'][$attribute] = array_merge($md2[$key][$transformEntity]['attributes'][$attribute], $rules);
                    } 
                }
                foreach($md2[$key][$transformEntity]['attributes'] as $i => $attribute) {
                    if (isset($attribute['type']) && $attribute['type'] === 'link') {
                        $tmp = $md2[$key][$transformEntity]['attributes'][$i]; 
                        unset($md2[$key][$transformEntity]['attributes'][$i]);
                        $md2[$key][$transformEntity]['attributes'][substr($i, 0, -3)] = $tmp;
                    }
                }
            

                if (method_exists($model, 'getDetails')) {
                    foreach($model->details as $detailKey => $detail) {
                        $modelClass = $detail;
                        $model = new $modelClass();
                        foreach($model->attributeLabels() as $keyAttr => $valueAttr) {
                            $md2[$key][$transformEntity]['details'][$detailKey]['attributes'][$keyAttr]['label'] = $valueAttr;
                        }

                        foreach($model->getValidators() as $validator) {
                            $rules = [];
                            foreach($validator->attributes as $attribute) {
                                $rules = $this->getValidationRules($attribute, $validator);
                                $rules  =  array_merge($md2[$key][$transformEntity]['details'][$detailKey]['attributes'][$attribute], $rules);
                                $md2[$key][$transformEntity]['details'][$detailKey]['attributes'][$attribute] = $rules;
                            }
                        }

                        foreach($md2[$key][$transformEntity]['details'][$detailKey]['attributes'] as $i => $attribute) {
                            if (isset($attribute['type']) && $attribute['type'] === 'link') {
                                $tmp = $md2[$key][$transformEntity]['details'][$detailKey]['attributes'][$i]; 
                                unset($md2[$key][$transformEntity]['details'][$detailKey]['attributes'][$i]);
                                $md2[$key][$transformEntity]['details'][$detailKey]['attributes'][substr($i, 0, -3)] = $tmp;
                            }
                        }

                    }   
                }
            }
        }
        return array_merge($md2, $this->other);  
    }

}
