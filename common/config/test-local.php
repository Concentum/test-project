<?php
return [
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [ 
            'dsn' => 'pgsql:host=localhost;dbname=yii2-api-sklad-test',
            'username' => 'postgres',
            'password' => 'secret',
          //  'charset' => 'utf8',
            'schemaMap' => [
                'pgsql'=> [
                    'class'=>'yii\db\pgsql\Schema',
                    'defaultSchema' => 'public'
                ]
            ],

            'enableSchemaCache' => true,
            'schemaCacheDuration' => 3600,
            'schemaCache' => 'cache',
                 /*
            'dsn' => 'mysql:host=localhost;dbname=yii2apisklad',
            'username' => 'root',
            'password' => 'secret',
            */
        ],
    ],
];
