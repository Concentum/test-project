<?php
namespace api\controllers\base;

use yii\rest\ActiveController;

class DocumentController extends ProtoController
{  
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'dataFilter' => [
                    'class' => \yii\data\ActiveDataFilter::class,
                    'searchModel' => $this->searchModel(),
                    'queryOperatorMap' => $this->modelClass::getDB()->driverName === 'pgsql' ? ['LIKE' => 'ILIKE'] : null
                ]
            ], 
        ]);
    }

    function searchModel()
    {
        return (new \yii\base\DynamicModel([
            'id' => null,
            'is_deleted' => null,
            'is_posted' => null,
            'number' => null,
            'date_time' => null
        ]))->addRule('id', 'integer')
        ->addRule('is_deleted', 'integer')
        ->addRule('is_posted', 'integer')
        ->addRule('number', 'string')
        ->addRule('date_time', 'string');
    }
}
