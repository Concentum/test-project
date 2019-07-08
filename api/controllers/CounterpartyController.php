<?php
namespace api\controllers;

use api\models\Counterparty;

/**
 * CounterpartyController implements the CRUD actions for Counterparty model.
 */
class CounterpartyController extends base\HierarchicalReferenceController
{
    public $modelClass = 'api\models\Counterparty';

    public function actions()
    {
       return array_merge(parent::actions(), [
       ]);
    }
}
