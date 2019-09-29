<?php
namespace api\controllers\reports;

use yii\rest\ActiveController;

class GoodsInWarehouseRemainsTurnoverController extends ActiveController
{
    public $modelClass = '\api\models\reports\GoodsInWarehouse';

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
                    $filter = new \yii\data\ActiveDataFilter([
                        'searchModel' => function () {
                            return (new \yii\base\DynamicModel([
                                't1.warehouse_id' => null,
                                't1.product_id' => null,
                                'recorder_id' => null,
                                'recorder_type' => null,
                                't1.period' => null,
                            ]))->addRule('t1.warehouse_id', 'integer')
                            ->addRule('t1.product_id', 'integer')
                            ->addRule('recorder_id', 'integer')
                            ->addRule('recorder_type', 'integer')
                            ->addRule('t1.period', 'datetime', ['format' => 'php:Y-m-d H:i:s']);
                        }    
                    ]);
                    
//?filter[product_id]=2&filter[warehouse_id]=1&filter[period][gte]=2019-01-01 00:00:00&filter[period][lte]=2019-12-31 23:59:59&detailing=day                    

                    $filterCondition = null;
                    
                    $q = preg_replace(
                        ['/warehouse_id/', '/product_id/', '/period/'],
                        ['t1.warehouse_id', 't1.product_id', 't1.period'], 
                        json_encode(\Yii::$app->request->get())
                    );

                    if ($filter->load(json_decode($q, true))) { 
                        $filterCondition = $filter->build();
                        if ($filterCondition === false) {
                            return $filter;
                        }
                    }

                    $detailing = \Yii::$app->request->get('detailing');
                    $detailing = isset($detailing) ? $detailing : 'millennium';
                    if (!in_array($detailing, [
                        'microseconds','milliseconds','second','minute','hour','day','week','month','quarter','year','decade','century','millennium'
                    ])) {
                        \Yii::$app->response->setStatusCode(422, 'Data Validation Failed.');
                        throw new \yii\web\HttpException(422, 'Data Validation Failed.');
                    }

                    $query = $this->modelClass::find()->with(['product', 'warehouse'])
                        ->select([
'date_trunc(:detailing, t1.period) as period', 't1.product_id', 't1.warehouse_id', 
'SUM(coalesce(t2.quantity, 0) - case when op = 1 then t1.quantity else 0 end + case when op = 2 then -t1.quantity else 0 end) as begin_quantity',
'SUM(case when op = 1 then t1.quantity else 0 end) as incoming',
'SUM(case when op = 2 then -t1.quantity else 0 end) as expense',
'coalesce(t2.quantity, 0) as end_quantity'])->from(['t1' => 'goods_in_warehouse'])
                ->leftJoin(['t2' => '(select product_id, warehouse_id, period, quantity
                              from goods_in_warehouse_total t1
                              where period in (
                              select max(period) from goods_in_warehouse_total
                              where goods_in_warehouse_total.warehouse_id = t1.warehouse_id
                              and goods_in_warehouse_total.product_id = t1.product_id
                              and date_trunc(:detailing, goods_in_warehouse_total.period) = date_trunc(:detailing, t1.period)))'],
't1.product_id = t2.product_id 
and t1.warehouse_id = t2.warehouse_id
and date_trunc(:detailing, t1.period) = date_trunc(:detailing, t2.period)')
->groupBy(['date_trunc(:detailing, t1.period), t1.product_id, t1.warehouse_id , coalesce(t2.quantity, 0)']) 
->addParams(['detailing' => $detailing]);

                    if ($filterCondition !== null) {
                        $query->andWhere($filterCondition);
                    }  

                    return \Yii::createObject([
                        'class' => \yii\data\ActiveDataProvider::class,
                        'query' => $query,
                    ]);
                    
                },
            ]
        ]);
    }

    public function checkAccess($action, $model = null, $params = [])
    {  
        if (!\Yii::$app->user->can($action.basename($this->modelClass)))
        throw new \yii\web\ForbiddenHttpException();
    }
    
}
