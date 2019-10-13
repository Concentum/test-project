<?php
namespace api\controllers\base;

use yii\rest\ActiveController;

class SimpleReferenceController extends ProtoController
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
            ]
        ]);
    }

    public function searchModel() {
        return (new \yii\base\DynamicModel([
            'id' => null,
            'code' => null,
            'description' => null,
            'is_deleted' => null,
            'author_id' => null
        ]))->addRule('id', 'integer')
        ->addRule('code', 'string')
        ->addRule('description', 'string')
        ->addRule('is_deleted', 'integer')
        ->addRule('author_id', 'integer');
    }   
}
