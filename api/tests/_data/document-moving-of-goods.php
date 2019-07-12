<?php
use yii\db\Expression;

return [ 
    [
        'number' => '00000001',
        'date_time' =>  new Expression('NOW()'),
        'is_deleted' => false,
        'is_posted' => true,
        'source_id' => 2,
        'destination_id' => 1,
        'version' => new Expression('NOW()'),
        'author_id' => 1,
    ],

    [
        'number' => '00000002',
        'date_time' =>  new Expression('NOW()'),
        'is_deleted' => false,
        'is_posted' => true,
        'source_id' => 2,
        'destination_id' => 1,
        'version' => new Expression('NOW()'),
        'author_id' => 2
    ], 

    [
        'number' => '00000003',
        'date_time' =>  new Expression('NOW()'),
        'is_deleted' => false,
        'is_posted' => true,
        'source_id' => 2,
        'destination_id' => 2,
        'version' => new Expression('NOW()'),
        'author_id' => 2
    ],

    [
        'number' => '00000004',
        'date_time' =>  new Expression('NOW()'),
        'is_deleted' => false,
        'is_posted' => true,
        'source_id' => 3,
        'destination_id' => 2,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ]
  
];
