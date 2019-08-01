<?php
namespace api\controllers\references;

/**
 * WarhouseController implements the CRUD actions for Warehouse model.
 */
class WarehouseController extends \api\controllers\base\SimpleReferenceController
{
    public $modelClass = 'api\models\references\Warehouse';
    
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
