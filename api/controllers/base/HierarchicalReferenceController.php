<?php
namespace api\controllers\base;

class HierarchicalReferenceController extends SimpleReferenceController
{
    public $modelClass;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
        ]);
    }
    
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
    
    public function searchModel()
    {
        $sm = parent::searchModel();
        $sm->addRule(['is_folder', 'parent_id'], 'integer');
        $sm->defineAttribute('is_folder', $value = null);
        $sm->defineAttribute('parent_id', $value = null);
        return $sm;
    }
}
