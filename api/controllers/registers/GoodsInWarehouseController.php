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
    /*        $behaviors['authenticator'] = [
              'class' => \yii\filters\auth\HttpBearerAuth::className(),
            ], */
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
                'prepareDataProvider' => function () {
                    return new \yii\data\ActiveDataProvider([
                        'query' => $this->modelClass::find()->with(['product', 'warehouse'])
                            ->select([
                            't1.product_id', 't1.warehouse_id',
                            'SUM(CASE WHEN op = 1 THEN t2.quantity - t1.quantity ELSE t2.quantity + t1.quantity END) AS begin_quantity',
                            't2.quantity AS end_quantity',
                            'SUM(CASE WHEN op = 1 THEN t1.quantity END) AS incoming',
                            'SUM(CASE WHEN op = 2 THEN -t1.quantity END) AS expense'
                            ])->from('goods_in_warehouse t1')
                            ->leftJoin('goods_in_warehouse_total t2',
                                   't1.product_id = t2.product_id AND t1.warehouse_id=t2.warehouse_id')
                        //    ->where()
                            ->groupBy(['t1.product_id', 't1.warehouse_id', 't2.quantity']),
                    ]);
                 },   

                'dataFilter' => [
                    'class' => \yii\data\ActiveDataFilter::class, 
                    'searchModel' => function () {
                        return (new \yii\base\DynamicModel([
                            'warehouse_id' => null,
                            'product_id' => null,
                            'recorder_id' => null,
                            'recorder_type' => null,
                            'date_begin' => null,
                            'date_end' => null
                        ]))->addRule('warehouse_id', 'integer')
                        ->addRule('product_id', 'integer')
                        ->addRule('recorder_id', 'integer')
                        ->addRule('recorder_type', 'integer')
                        ->addRule('date_begin', 'integer')
                        ->addRule('date_end', 'integer');
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
