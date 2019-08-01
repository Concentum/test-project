<?php
namespace api\controllers;

use yii\rest\ActiveController;

class PropertyValueController extends ActiveController
{
    public $modelClass = '\api\models\PropertyValue';

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
        /*    $behaviors['authenticator'] = [
              'class' => \yii\filters\auth\HttpBearerAuth::className(),
            ],*/
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
                            'property_id' => null,
                            'object_id' => null,
                            'period' => null,
                            'value' => null,
                        ]))->addRule('id', 'integer')
                        ->addRule('property_id', 'string')
                        ->addRule('object_id', 'string')
                        ->addRule('period', 'string')
                        ->addRule('value', 'string');;
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
