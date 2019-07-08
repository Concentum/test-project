<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
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
                'auth' => 'auth/auth',
                'signup' => 'auth/signup',
                'profile' => 'auth/profile',
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'counterparty'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'product'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'warehouse'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'document_coming_of_goods'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'document_expend_of_goods'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'document_move_of_goods'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'object_property'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'property_value'],
            ],
        ],
        
    ],
    'params' => $params,
];
