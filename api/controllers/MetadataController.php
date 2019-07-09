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
        'documents' => ['DocumentComingOfGoods', 'DocumentExpendOfGoods', 'DocumentMovingOfGoods']
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
        foreach($this->entitys as $key => $value) {
            foreach($value as $entity) {
                $modelClass = $this->nspace.'\\'.$entity;
                $model = new $modelClass();
                foreach($model->attributeLabels() as $keyAttr => $valueAttr) {
                    $md[$key][$entity]['attributes'][$keyAttr]['label'] = $valueAttr;
                }
                foreach($model->getValidators() as $keyValidator => $valueValidator) {
                    $tmp = $valueValidator->attributes;
                    unset($valueValidator->attributes);
                    foreach($valueValidator as $propKey => $propValue) {
                        if (is_null($propValue) || $propValue === []) {
                            unset($valueValidator->$propKey);
                        }
                    }
                    foreach($tmp as $i => $attr) {
                        $md[$key][$entity]['attributes'][$attr]['validators'] = $valueValidator; //basename($valueValidator->targetClass);
                    }
                }
                if (method_exists($model, 'details')) {
                    foreach($model->details() as $detailKey => $detail) {
                        $modelClass = $detail;
                        $model = new $modelClass();
                        foreach($model->attributeLabels() as $keyAttr => $valueAttr) {
                            $md[$key][$entity]['details'][$detailKey]['attributes'][$keyAttr]['label'] = $valueAttr;
                        }
                        foreach($model->getValidators() as $keyValidator => $valueValidator) {
                            $tmp = $valueValidator->attributes;
                            unset($valueValidator->attributes);
                            foreach($valueValidator as $propKey => $propValue) {
                                if (is_null($propValue) || $propValue === []) {
                                    unset($valueValidator->$propKey);
                                }
                            }
                            foreach($tmp as $i => $attr) {
                                $md[$key][$entity]['details'][$detailKey]['attributes'][$attr]['validators'] = $valueValidator; //basename($valueValidator->targetClass);
                            }
                        }
                    }   
                }   
            }
        }
        return $md;  
    }

}
