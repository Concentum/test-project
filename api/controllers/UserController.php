<?php
namespace api\controllers;

use yii\rest\ActiveController;
use yii\db\Query;


class UserController extends ActiveController
{
    public $modelClass = 'api\models\User';

   	public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

/*
    public function behaviors()
    {
       // $behaviors = parent::behaviors();
        $behaviors[] =  [
           'class' => \yii\filters\Cors::className(),
        ];
        $behaviors[] =  [
           'class' => \yii\filters\auth\HttpBearerAuth::className(),
        ];
        \Yii::info($behaviors);
        return $behaviors;
    } 
*/


    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            $behaviors['corsFilter'] = [
              'class' => \yii\filters\Cors::className(),
            ],
            $behaviors['authenticator'] = [
              'class' => \yii\filters\auth\HttpBearerAuth::className(),
            ],
        ]); 
        return $behaviors;
    } 
    


    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'dataFilter' => [
                    'class' => \yii\data\ActiveDataFilter::class,
                    'searchModel' => $this->searchModel(),
                    'queryOperatorMap' => $this->modelClass::getDB()->driverName === 'pgsql' ? ['LIKE' => 'ILIKE'] : null
                ]
            ]
       ]);
    }

    public function searchModel() {
        return (new \yii\base\DynamicModel([
            'username' => null,
            'email' => null
        ]))->addRule('username', 'string')
        ->addRule('email', 'string');
    }

    public function checkAccess($action, $model = null, $params = [])
    {   \Yii::info(\Yii::$app->user->can($action.basename($this->modelClass)));
        if (!\Yii::$app->user->can($action.basename($this->modelClass)))
        throw new \yii\web\ForbiddenHttpException(); 
    }      

}


