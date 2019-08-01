<?php
namespace api\controllers\documents;

/**
 * MovingOfGoodsController implements the CRUD actions for MovingOfGoods model.
 */
class MovingOfGoodsController extends \api\controllers\base\DocumentController
{
    public $modelClass = 'api\models\documents\MovingOfGoods';

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
        $sm->addRule(['source_id', 'destination_id'], 'integer');
        $sm->defineAttribute('source_id', $value = null);
        $sm->defineAttribute('destination_id', $value = null);
        return $sm;
    }    
}
