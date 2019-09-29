<?php
namespace api\controllers;

use Yii;
use yii\rest\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use api\models\auth\LoginForm;
use api\models\auth\SignupForm;


use yii\web\BadRequestHttpException;
/**
 * Auth controller
 */
class AuthController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'auth'  => ['POST'],
                    'signup'   => ['POST'],
                ]
            ]
        ];
    }
    
    public function actionAuth()
    {   
        $model = new LoginForm();
        $model->load(Yii::$app->request->bodyParams, '');
        if ($token = $model->auth()) {
            return $token;
        } else {
            return $model;
        }
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        $model->load(Yii::$app->request->bodyParams, '');
        
        if ($token = $model->signup()) {
            return $token;
        } else {
            return $model;
        }
    }

    public function actionProfile()
    {
        $model = new SignupForm();
        $model->load(Yii::$app->request->bodyParams, '');
        
        if ($token = $model->signup()) {
            return $token;
        } else {
            return $model;
        }
    }
    
}
