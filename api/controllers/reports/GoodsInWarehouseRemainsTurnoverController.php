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
                                'warehouse_id' => null,
                                'product_id' => null,
                                'recorder_id' => null,
                                'recorder_type' => null,
                                'period' => null,
                                
                            ]))->addRule('warehouse_id', 'integer')
                            ->addRule('product_id', 'integer')
                            ->addRule('recorder_id', 'integer')
                            ->addRule('recorder_type', 'integer')
                            ->addRule('period', 'datetime', ['format' => 'php:Y-m-d H:i:s']);    
                        }    
                    ]);
                    
//?filter[product_id][in][]=2&filter[product_id][in][]=3&filter[warehouse_id]=1&filter[period][gte]=2019-01-01%2000:00:00&filter[period][lte]=2019-12-31%2023:59:59&detailing=day

                    $filterCondition = null;
         \Yii::info(\Yii::$app->request->get());

                    if ($filter->load(\Yii::$app->request->get())) { 
                        $filterCondition = $filter->build();
                        if ($filterCondition === false) {
                            return $filter;
                        }
                        $cond = str_replace('SELECT * WHERE', '', (new \yii\db\Query())->where($filterCondition)->createCommand()->getRawSql());
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
                    ->select(['*'])->from(['goods_in_warehouse_remains_and_turnover(:_condition, :detailing)'])
                    ->addParams([
                        '_condition' => $cond,
                        'detailing' => $detailing
                    ]);

                    return \Yii::createObject([
                        'class' => \yii\data\ActiveDataProvider::class,
                        'query' => $query
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
