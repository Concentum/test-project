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
        $sm = parent::searchModel();
        $sm->addRule(['counterparty_id', 'warehouse_id'], 'integer');
        $sm->defineAttribute('counterparty_id', $value = null);
        $sm->defineAttribute('warehouse_id', $value = null);
        return $sm;
    }    

}
