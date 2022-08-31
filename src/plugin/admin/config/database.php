<?php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => '3306',
            'database'    => 'workerman_net',
            'username'    => 'root',
            'password'    => 'P@ssword',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];