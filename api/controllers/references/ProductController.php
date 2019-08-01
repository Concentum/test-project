<?php
namespace api\controllers\references;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends \api\controllers\base\HierarchicalReferenceController
{
    public $modelClass = 'api\models\references\Product';

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
