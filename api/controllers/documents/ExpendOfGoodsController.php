<?php
namespace api\controllers\documents;

/**
 * ExpendOfGoodsController implements the CRUD actions for ExpendOfGoods model.
 */
class ExpendOfGoodsController extends \api\controllers\base\DocumentController
{
    public $modelClass = 'api\models\documents\ExpendOfGoods';

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
