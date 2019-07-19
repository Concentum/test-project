<?php
namespace api\controllers\base;

use yii\rest\ActiveController;

class ProtoController extends ActiveController
{
    public $modelClass;
    
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
            'create' => [
                'class' => 'api\controllers\base\actions\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'update' => [
                'class' => 'api\controllers\base\actions\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ] 
        ]);
    }

    public function checkAccess($action, $model = null, $params = [])
    {   
        if (!\Yii::$app->user->can($action.basename($this->modelClass)))
        throw new \yii\web\ForbiddenHttpException();
    }   
}
