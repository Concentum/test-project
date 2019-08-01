<?php
namespace api\controllers\references;

/**
 * CounterpartyController implements the CRUD actions for Counterparty model.
 */
class CounterpartyController extends \api\controllers\base\HierarchicalReferenceController
{
    public $modelClass = 'api\models\references\Counterparty';

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
        ]);
    }
/*    
    public function actions()
    {  \Yii::info(parent::actions());
       return array_merge(parent::actions(), [
       ]);
    }
*/    
}
