<?php
return [
    'components' => [
        'db' => [ 
            'dsn' => 'pgsql:host=localhost;dbname=yii2-api-sklad',
            'username' => 'postgres',
            'password' => 'secret',
          //  'charset' => 'utf8',
            'schemaMap' => [
                'pgsql'=> [
                    'class'=>'yii\db\pgsql\Schema',
                    'defaultSchema' => 'public'
                ]
            ]     /*
            'dsn' => 'mysql:host=localhost;dbname=yii2apisklad',
            'username' => 'root',
            'password' => 'secret',
            */
        ],
    ],
];
