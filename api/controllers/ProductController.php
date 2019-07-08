<?php
namespace api\controllers;

use yii\rest\ActiveController;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends base\HierarchicalReferenceController
{
    public $modelClass = '\api\models\Product';

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
