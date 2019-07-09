<?php
namespace api\controllers;

use Yii;
use yii\rest\Controller;

/**
 * Metadata controller
 */
class MetadataController extends Controller
{
    private $nspace = 'api\models';
    private $modelsClasses = [
        'references' => ['Product', 'Counterparty', 'Warehouse'],
        'documents' => ['DocumentComingOfGoods', 'DocumentExpendOfGoods', 'DocumentMovingOfGoods']
    ];

    /**
    * {@inheritdoc}
    */
    public function behaviors()
    {
      return [
          'corsFilter' => [
              'class' => \yii\filters\Cors::className(),
          ],
    /*      'authenticator' => [
            'class' => \yii\filters\auth\HttpBearerAuth::className(),
          ], */
      ];
    
    }
    
    public function actionIndex()
    {   
        return $this->modelsClasses;  
    }

}
