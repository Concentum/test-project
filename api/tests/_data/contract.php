<?php
use yii\db\Expression;

return [

    [
        'code' => '00000002',
        'description' => '№1 ООО Фабула',
        'is_deleted' => false,
        'date' =>  new Expression('NOW()'),
        'number' => '1',
        'expires_at' =>  new Expression('NOW()'),
        'contract_type' => 1,
        'counterparty_id' => 2,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],

    [
        'code' => '00000003',
        'description' => '№2 ЗАО Одуванчик',
        'is_deleted' => false,
        'date' =>  new Expression('NOW()'),
        'number' => '2',
        'expires_at' =>  new Expression('NOW()'),
        'contract_type' => 1,
        'counterparty_id' => 3,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],
  
];
