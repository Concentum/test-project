<?php
namespace api\controllers;

use yii\rest\ActiveController;
use yii\db\Query;


class UserController extends ActiveController
{
    public $modelClass = 'api\models\User';

   	public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];


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

        $behaviors[ 'access'] = [
            'class' => \yii\filters\AccessControl::className(),
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
              ],
            ],
        ];  */
	    return $behaviors;
    }

}


