<?php
namespace api\controllers;

use api\models\Counterparty;

/**
 * CounterpartyController implements the CRUD actions for Counterparty model.
 */
class CounterpartyController extends base\HierarchicalReferenceController
{
    public $modelClass = 'api\models\Counterparty';

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
