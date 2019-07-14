<?php

return [
    [
        'username' => $faker->name,
        'email' => $faker->email,
        'password' => Yii::$app->getSecurity()->generatePasswordHash('password_' . $index),
        'auth_key' => Yii::$app->getSecurity()->generateRandomString(),
        'status' => 10
    ]
];
