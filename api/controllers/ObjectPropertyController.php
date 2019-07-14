<?php
namespace api\controllers;

use yii\rest\ActiveController;

class ObjectPropertyController extends ActiveController
{
    public $modelClass = '\api\models\ObjectProperty';

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            $behaviors['corsFilter'] = [
              'class' => \yii\filters\Cors::className(),
            ],
            $behaviors['authenticator'] = [
              'class' => \yii\filters\auth\HttpBearerAuth::className(),
            ],
        ]); 
    }

    public function actions()
    {   
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'dataFilter' => [
                    'class' => \yii\data\ActiveDataFilter::class,
                    'searchModel' => function () {
                        return (new \yii\base\DynamicModel([
                            'id' => null,
                            'object_class' => null,
                            'property_name' => null,
                            'property_label' => null
                        ]))->addRule('id', 'integer')
                        ->addRule('object_class', 'string')
                        ->addRule('property_name', 'string')
                        ->addRule('property_label', 'string');
                    }   
                ]
            ]
        ]);
    }

    public function checkAccess($action, $model = null, $params = [])
    {   \Yii::info(\Yii::$app->user->can($action.basename($this->modelClass)));
        if (!\Yii::$app->user->can($action.basename($this->modelClass)))
        throw new \yii\web\ForbiddenHttpException();
    }
    
}
