<?php
namespace api\controllers;

use api\models\DocumentComingOfGoods;

/**
 * DocumentComingOfGoodsController implements the CRUD actions for DocumentComingOfGoods model.
 */
class DocumentComingOfGoodsController extends base\DocumentController
{
    public $modelClass = 'api\models\DocumentComingOfGoods';
    
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
        ]);
    }

    public function actions()
    {
       return array_merge(parent::actions(), [
           $actions['index'] = [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'dataFilter' => [
                    'class' => \yii\data\ActiveDataFilter::class,
                    'searchModel' => function () {
                        return (new \yii\base\DynamicModel([
                            'counterparty_id' => null,
                            'warehouse_id' => null,
                        ]))->addRule('counterparty_id', 'integer')
                        ->addRule('warehouse_id', 'integer');
                    }
                ]
            ]
       ]);
    }

}
