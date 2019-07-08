<?php
use yii\db\Expression;

return [
    [
        'code' => '00000001',
        'description' => 'Ноутбуки',
        'is_deleted' => false,
        'is_folder' => true,
        'parent_id' => null,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],

    [
        'code' => '00000002',
        'description' => 'Ноутбук ASUS VivoBook S15',
        'is_deleted' => false,
        'is_folder' => false,
        'parent_id' => 1,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],

    [
        'code' => '00000003',
        'description' => 'Ноутбук Apple MacBook Air',
        'is_deleted' => false,
        'is_folder' => false,
        'parent_id' => 1,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],
  
];
