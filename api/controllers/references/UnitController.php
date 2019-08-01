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
       ]);
    }
}
