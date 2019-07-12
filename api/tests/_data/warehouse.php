<?php
use yii\db\Expression;

return [
    [
        'code' => '00000001',
        'description' => 'Склад №1',
        'is_deleted' => false,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],

    [
        'code' => '00000002',
        'description' => 'Склад №2',
        'is_deleted' => false,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],

    [
        'code' => '00000003',
        'description' => 'Склад №3',
        'is_deleted' => false,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],
];
