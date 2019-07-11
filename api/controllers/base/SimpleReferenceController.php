<?php
namespace api\controllers\base;

use yii\rest\ActiveController;

class SimpleReferenceController extends ActiveController
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
            ], */
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
                    'searchModel' => $this->searchModel()
                ]
            ]
        ]);
    }

    public function checkAccess($action, $model = null, $params = [])
    {   \Yii::info(\Yii::$app->user->can($action.basename($this->modelClass)));
        if (!\Yii::$app->user->can($action.basename($this->modelClass)))
        throw new \yii\web\ForbiddenHttpException();
    }
 
    public function searchModel() {
        return (new \yii\base\DynamicModel([
            'id' => null,
            'code' => null,
            'description' => null,
            'is_deleted' => null
        ]))->addRule('id', 'integer')
        ->addRule('code', 'string')
        ->addRule('description', 'string')
        ->addRule('is_deleted', 'integer');
    }   
}
