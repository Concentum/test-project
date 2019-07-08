<?php
namespace api\controllers;

use api\models\DocumentMovingOfGoods;

/**
 * DocumentMovingOfGoodsController implements the CRUD actions for DocumentMovingOfGoods model.
 */
class DocumentMovingOfGoodsController extends base\DocumentController
{
    public $modelClass = 'api\models\DocumentMovingOfGoods';

    public function actions()
    {
        return array_merge(parent::actions(), [
            $actions['index'] = [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'dataFilter' => [
                    'class' => \yii\data\ActiveDataFilter::class,
                    'searchModel' => function () {
                        return (new \yii\base\DynamicModel([
                            'source_id' => null,
                            'destination_id' => null,
                        ]))->addRule('source_id', 'integer')
                        ->addRule('destination_id', 'integer');
                    }
                ]
            ]
        ]);    
    }
}
