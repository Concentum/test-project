<?php

return [ 
    [
        'number' => sprintf("%08d", $index + 1),
        'date_time' => time(),
        'is_deleted' => false,
        'is_posted' => false,
        'counterparty_id' => 2,
        'warehouse_id' => 1,
        'version' => time(),
        'author_id' => 1,
    ]
];
