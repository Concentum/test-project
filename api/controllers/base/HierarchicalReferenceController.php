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
        $actions = parent::actions();
        $custom_actions = [
            'index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'dataFilter' => [
                    'class' => \yii\data\ActiveDataFilter::class,
                    'searchModel' => $this->searchModel()
                ]
            ]
        ];
        return array_merge($actions, $custom_actions);
    }
    
    public function searchModel() {
        $sm = parent::searchModel();
        $sm->addRule(['is_folder', 'parent_id'], 'integer');
        $sm->defineAttribute('is_folder', $value = null);
        $sm->defineAttribute('parent_id', $value = null);
        return $sm;
    }
}
