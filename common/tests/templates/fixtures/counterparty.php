<?php

return [
    [
        'code' =>  sprintf("%08d", $index + 1),
        'description' => $faker->company,
        'is_deleted' => false,
        'is_folder' => false,
        'parent_id' => null,
        'author_id' => $index + 1
    ]
];
