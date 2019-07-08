<?php
namespace api\controllers\base;

use yii\rest\ActiveController;

class SimpleReferenceController extends ActiveController
{
    public $modelClass;

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            $behaviors['corsFilter'] = [
              'class' => \yii\filters\Cors::className(),
            ],
            $behaviors['authenticator'] = [
              'class' => \yii\filters\auth\HttpBearerAuth::className(),
            ], 
        ]); 
    }

    public function actions()
    {   
        return array_merge(parent::actions(), 
            ['index' => [
                'class' => 'yii\rest\IndexAction',
                'modelClass' => $this->modelClass,
                'dataFilter' => [
                    'class' => \yii\data\ActiveDataFilter::class,
                    'searchModel' => function () {
                        return (new \yii\base\DynamicModel([
                            'id' => null,
                            'code' => null,
                            'description' => null,
                            'is_deleted' => null
                        ]))->addRule('id', 'integer')
                        ->addRule('code', 'string')
                        ->addRule('description', 'string')
                        ->addRule('is_deleted', 'integer');
                    }
                ]
            ]    
        ]);
    }

}
