<?php
namespace api\controllers\registers;

use yii\rest\ActiveController;

class GoodsInWarehouseController extends ActiveController
{
    public $modelClass = '\api\models\registers\GoodsInWarehouse';

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
        $actions = parent::actions();
        unset($actions['create'], $actions['update'], $actions['delete']);
        return array_merge($actions, [
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

    public function searchModel()
    {
        return (new \yii\base\DynamicModel([
                'warehouse_id' => null,
                'product_id' => null,
                'recorder_id' => null,
                'recorder_type' => null,
                'date_begin' => null,
                'date_end' => null,
            ]))->addRule('warehouse_id', 'integer')
            ->addRule('product_id', 'integer')
            ->addRule('recorder_id', 'integer')
            ->addRule('recorder_type', 'integer')
            ->addRule('date_begin', 'datetime', ['format' => 'php:Y-m-d H:i:s'])
            ->addRule('date_end', 'datetime', ['format' => 'php:Y-m-d H:i:s']);
    }    

    public function checkAccess($action, $model = null, $params = [])
    {  
        if (!\Yii::$app->user->can($action.basename($this->modelClass)))
        throw new \yii\web\ForbiddenHttpException();
    }
    
}
