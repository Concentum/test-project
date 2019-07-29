<?php
namespace api\controllers;

use Yii;
use yii\rest\Controller;

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
                        ["label" => "Приход товара", "endpoint" => "documents.document_coming_of_goods"],
                        ["label" => "Расход товара", "endpoint" => "documents.document_expend_of_goods"]
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
    
    public function actionIndex()
    {   
        $properties = \api\models\ObjectProperty::find()->asArray()->all();
        $excludeValidators = ['enableClientValidation', 'forceMasterDb', 'targetAttributeJunction'];
        foreach($this->entitys as $key => $value) {
            foreach($value as $entity) {
                $modelClass = $this->nspace.'\\'.$entity;
                $model = new $modelClass();
                $md[$key][$entity]['title'] = $model->title();
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
                    $tmp = null;
                    foreach($dm->getValidators() as $validator) {
                        unset($validator->attributes);
                        $tmp = $validator;
                    } 
                    $md[$key][$entity]['properties'][$v['property_name']] = ['label' => $v['property_label'], 'validators' => $tmp];
                }

                foreach($model->attributeLabels() as $keyAttr => $valueAttr) {
                    $md[$key][$entity]['attributes'][$keyAttr]['label'] = $valueAttr;
                }
                foreach($model->getValidators() as $keyValidator => $valueValidator) {
                    $tmp = $valueValidator->attributes;
                    unset($valueValidator->attributes);
                    foreach($valueValidator as $propKey => $propValue) {
                        if (is_null($propValue) || $propValue === [] || in_array($propKey, $excludeValidators)) {
                            unset($valueValidator->$propKey);
                        }
                    }
                    foreach($tmp as $i => $attr) {
                        if (isset($valueValidator->targetClass)) {
                            $valueValidator->targetClass = basename($valueValidator->targetClass);
                        }
                        $md[$key][$entity]['attributes'][$attr]['validators'] = $valueValidator; 
                    }
                }
                if (method_exists($model, 'getDetails')) {
                    foreach($model->details as $detailKey => $detail) {
                        $modelClass = $detail;
                        $model = new $modelClass();
                        foreach($model->attributeLabels() as $keyAttr => $valueAttr) {
                            $md[$key][$entity]['details'][$detailKey]['attributes'][$keyAttr]['label'] = $valueAttr;
                        }
                        foreach($model->getValidators() as $keyValidator => $valueValidator) {
                            $tmp = $valueValidator->attributes;
                            unset($valueValidator->attributes);
                            foreach($valueValidator as $propKey => $propValue) {
                                if (is_null($propValue) || $propValue === [] || in_array($propKey, $excludeValidators)) {
                                    unset($valueValidator->$propKey);
                                }
                            }
                            foreach($tmp as $i => $attr) {
                                if (isset($valueValidator->targetClass)) {
                                    $valueValidator->targetClass = basename($valueValidator->targetClass);
                                }
                                $md[$key][$entity]['details'][$detailKey]['attributes'][$attr]['validators'] = $valueValidator;
                            }
                        }
                    }   
                }
                
            }
        }
        return array_merge($md, $this->other);  
    }

}
