<?php
namespace api\controllers\references;

/**
 * ContractController implements the CRUD actions for Contract model.
 */
class ContractController extends \api\controllers\base\SimpleReferenceController
{
    public $modelClass = 'api\models\references\Contract';
    
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
        $sm->addRule(['counterparty_id'], 'integer');
        $sm->defineAttribute('counterparty_id', $value = null);
        return $sm;
    }    
}
