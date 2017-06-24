<?php

include __DIR__.'/database.php';

return [
    'propel' => [
        'database' => [
            'connections' => [
                'money' => [
                    'adapter'    => 'mysql',
                    'classname'  => 'Propel\Runtime\Connection\ConnectionWrapper',
                    'dsn'        => 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['name'],
                    'user'       => $config['db']['user'],
                    'password'   => $config['db']['pass'],
                    'attributes' => [],
                ],
            ],
        ],
        'runtime' => [
            'defaultConnection' => 'money',
            'connections'       => ['money'],
        ],
        'generator' => [
            'defaultConnection' => 'money',
            'connections'       => ['money'],
        ],
    ],
];
