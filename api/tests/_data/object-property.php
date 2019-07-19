<?php
use yii\db\Expression;

return [
    [
        'object_class' => 'api\models\Product',
        'property_name' => 'origin-country',
        'property_label' => 'Страна происхождения',
        'property_rules' => json_encode(''),
        'is_periodic' => false
    ],
    [
        'object_class' => 'api\models\Counterparty',
        'property_name' => 'registered-address',
        'property_label' => 'Юридический адрес',
        'property_rules' => json_encode(''),
        'is_periodic' => false
    ],
    [
        'object_class' => 'api\models\Warehouse',
        'property_name' => 'warehouseman',
        'property_label' => 'Ответственное лицо',
        'property_rules' => json_encode(''),
        'is_periodic' => false
    ],
    [
        'object_class' => 'api\models\DocumentComingOfGoods',
        'property_name' => 'performer',
        'property_label' => 'Исполнитель',
        'property_rules' => json_encode(''),
        'is_periodic' => false
    ]
];
