<?php
use yii\db\Expression;

return [

    [
        'code' => '00000001',
        'description' => 'Штука',
        'is_deleted' => false,
        'designation' => 'шт',
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],

    [
        'code' => '00000002',
        'description' => 'Упаковка',
        'is_deleted' => false,
        'designation' => 'уп',
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],
  
    [
        'code' => '00000003',
        'description' => 'Метр квадратный',
        'is_deleted' => false,
        'designation' => 'м2',
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],
  
    [
        'code' => '00000004',
        'description' => 'Метр кубический',
        'is_deleted' => false,
        'designation' => 'м3',
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],

    [
        'code' => '00000005',
        'description' => 'Метр погонный',
        'is_deleted' => false,
        'designation' => 'мп',
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],
  
  
];
