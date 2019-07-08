<?php
namespace api\controllers;

use api\models\Warehouse;

/**
 * WarhouseController implements the CRUD actions for Warehouse model.
 */
class WarehouseController extends base\SimpleReferenceController
{
    public $modelClass = 'api\models\Warehouse';
    
    public function actions()
    {
       return array_merge(parent::actions(), [
       ]);
    }
}
