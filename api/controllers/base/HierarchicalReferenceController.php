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
        $behaviors = parent::behaviors();

        // add CORS filter
        $behaviors['corsFilter'] = [
          'class' => \yii\filters\Cors::className(),
        ];
/*
        $behaviors['authenticator'] = [
          'class' => \yii\filters\auth\HttpBearerAuth::className(),
        ];
*/
        return $behaviors;
    }

    
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
                            'is_folder' => null,
                            'parent_id' => null,
                        ]))->addRule('is_folder', 'integer')
                        ->addRule('parent_id', 'integer');
                    }
                ]
            ]
        ]);    
        return $actions;
    }
    
}
