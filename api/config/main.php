<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

function controllerMap() 
{
    return [
        'counterparty' => 'api\controllers\references\CounterpartyController',
        'product' => 'api\controllers\references\ProductController',
        'warehouse' => 'api\controllers\references\WarehouseController',
        'unit' => 'api\controllers\references\UnitController',
        'contract' => 'api\controllers\references\ContractController',
        'coming-of-goods' => 'api\controllers\documents\ComingOfGoodsController',
        'expend-of-goods' => 'api\controllers\documents\ExpendOfGoodsController',
        'moving-of-goods' => 'api\controllers\documents\MovingOfGoodsController',
        'goods-in-warehouse' => 'api\controllers\registers\GoodsInWarehouseController',
    ];
}

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'controllerMap' => controllerMap(),
    'components' => [
        'request' => [
        //    'csrfParam' => '_csrf-api',
            'baseUrl' => '',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'application/xml' => 'yii\web\XmlParser',
            ]
        ],
        'response' => [
            'format' => \yii\web\Response::FORMAT_JSON,
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, 
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
        ], 
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                'metadata' => 'metadata/index',
                'auth' => 'auth/auth',
                'signup' => 'auth/signup',
                'profile' => 'auth/profile',     

                ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'counterparty'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'product'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'warehouse'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'unit'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'contract'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'coming-of-goods'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'expend-of-goods'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'moving-of-goods'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'object-property'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'property-value'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'goods-in-warehouse'], 
            ],
        ],
        
    ],
    'params' => $params,
];
