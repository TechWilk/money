<?php

include __DIR__.'/../config/database.php';

$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
$serviceContainer->checkVersion('2.0.0-dev');
$serviceContainer->setAdapterClass('money', 'mysql');
$manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
$manager->setConfiguration([
  'classname'  => 'Propel\\Runtime\\Connection\\ConnectionWrapper',
  'dsn'        => 'mysql:host='.$config['db']['host'].';dbname='.$config['db']['name'],
  'user'       => $config['db']['user'],
  'password'   => $config['db']['pass'],
  'attributes' => [
    'ATTR_EMULATE_PREPARES' => false,
    'ATTR_TIMEOUT'          => 30,
  ],
  'model_paths' => [
    0 => 'src',
    1 => 'vendor',
  ],
]);
$manager->setName('money');
$serviceContainer->setConnectionManager('money', $manager);
$serviceContainer->setDefaultDatasource('money');
