<?php
use yii\db\Expression;

return [
    [
        'object_class' => 'api\models\references\Product',
        'property_name' => 'origin-country',
        'property_label' => 'Страна происхождения',
        'property_rules' => ["value","string", ["max" => 64]],
        'is_periodic' => false
    ],
    [
        'object_class' => 'api\models\references\Counterparty',
        'property_name' => 'registered-address',
        'property_label' => 'Юридический адрес',
        'property_rules' => ["value","string", ["max" => 64]],
        'is_periodic' => false
    ],
    [
        'object_class' => 'api\models\references\Warehouse',
        'property_name' => 'warehouseman',
        'property_label' => 'Ответственное лицо',
        'property_rules' => ["value","string", ["max" => 64]],
        'is_periodic' => false
    ],
    [
        'object_class' => 'api\models\documents\ComingOfGoods',
        'property_name' => 'performer',
        'property_label' => 'Исполнитель',
        'property_rules' => ["value","string", ["max" => 64]],
        'is_periodic' => false
    ]
];
