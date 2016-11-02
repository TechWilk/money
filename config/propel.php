<?php

$config['db']['name'] = 'dev_money';
$config['db']['host'] = '127.0.0.1';
$config['db']['user'] = 'root';
$config['db']['pass'] = 'local';

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
                    'attributes' => []
                ]
            ]
        ],
        'runtime' => [
            'defaultConnection' => 'money',
            'connections' => ['money']
        ],
        'generator' => [
            'defaultConnection' => 'money',
            'connections' => ['money']
        ]
    ]
];