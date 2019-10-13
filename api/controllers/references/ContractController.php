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
                    'searchModel' => $this->searchModel(),
                    'queryOperatorMap' => $this->modelClass::getDB()->driverName === 'pgsql' ? ['LIKE' => 'ILIKE'] : null
                ]
            ]
       ]);
    }

    public function searchModel()
    {
        $sm = parent::searchModel();
        $sm->addRule(['counterparty_id', 'contract_type'], 'integer');
        $sm->addRule(['date', 'expires_at'], 'datetime', ['format' => 'php:Y-m-d H:i:s']);
        $sm->addRule('number', 'string');
        $sm->defineAttribute('counterparty_id', $value = null);
        $sm->defineAttribute('contract_type', $value = null);
        $sm->defineAttribute('number', $value = null);
        $sm->defineAttribute('date', $value = null);
        $sm->defineAttribute('expires_at', $value = null);
        return $sm;
    }    
}
