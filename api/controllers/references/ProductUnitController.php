<?php
namespace api\controllers\references;

/**
 * ProductUnitController implements the CRUD actions for ProductUnit model.
 */
class ProductUnitController extends \api\controllers\base\SimpleReferenceController
{
    public $modelClass = 'api\models\references\ProductUnit';

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
