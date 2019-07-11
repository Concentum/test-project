<?php
namespace api\controllers\base;

use yii\rest\ActiveController;

class DocumentController extends ActiveController
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
    /*        $behaviors['authenticator'] = [
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
                            'is_deleted' => null,
                            'is_posted' => null,
                            'number' => null,
                            'date_time' => null
                        ]))->addRule('id', 'integer')
                        ->addRule('is_deleted', 'integer')
                        ->addRule('is_posted', 'integer')
                        ->addRule('number', 'string')
                        ->addRule('date_time', 'string');
                    }
                ]
            ], /*
            'create' => [
                'class' => 'app\controllers\base\actions\CreateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'update' => [
                'class' => 'app\controllers\base\actions\UpdateAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ] */
        ]);
    }

    public function checkAccess($action, $model = null, $params = [])
    {   
        if (!\Yii::$app->user->can($action.basename($this->modelClass)))
        throw new \yii\web\ForbiddenHttpException();
    }   
}
