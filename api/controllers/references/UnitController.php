<?php
namespace api\controllers\references;

/**
 * UnitController implements the CRUD actions for Unit model.
 */
class UnitController extends \api\controllers\base\SimpleReferenceController
{
    public $modelClass = 'api\models\references\Unit';
    
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
        $sm->addRule(['designation'], 'string');
        $sm->defineAttribute('designation', $value = null);
        return $sm;
    }
}
