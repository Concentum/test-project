<?php
use yii\db\Expression;

return [
    [
        'code' => '00000001',
        'description' => 'Юридические лица',
        'is_deleted' => false,
        'is_folder' => true,
        'parent_id' => null,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],

    [
        'code' => '00000002',
        'description' => 'ООО Фабула',
        'is_deleted' => false,
        'is_folder' => false,
        'parent_id' => 1,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],

    [
        'code' => '00000003',
        'description' => 'ЗАО Одуванчик',
        'is_deleted' => false,
        'is_folder' => false,
        'parent_id' => 1,
        'version' => new Expression('NOW()'),
        'author_id' => 1
    ],
  
];
