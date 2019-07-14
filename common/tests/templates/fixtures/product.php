<?php
$goods = ['Ноутбуки', 'Ноутбук ASUS VivoBook S15', 'Ноутбук Apple MacBook Air' , 'Ноутбук Lenovo 330-15IKB', 'Ноутбук Acer Aspire A315-21G-944Q'];

return [
    [
        'code' =>  sprintf("%08d", $index + 1),
        'description' => $goods[$index],
        'is_deleted' => false,
        'is_folder' => false,
        'parent_id' => $index == 0 ? null : 1, 
        'author_id' => $index + 1
    ]
];
  