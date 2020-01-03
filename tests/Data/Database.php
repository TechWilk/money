<?php

namespace TechWilk\Money\Tests\Data;

class Database
{
    private $connectionName = 'money';
    private $adaptor = 'mysql';
    private $dsn = 'mysql:host=127.0.0.1;dbname=money_test';
    private $user = 'travis';
    private $password = 'really-secret';

    public function __construct(TestData $testData = null)
    {
        /**
         * PROPEL ORM CONFIG.
         */
        $serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
        $serviceContainer->checkVersion('2.0.0-dev');
        $serviceContainer->setAdapterClass($this->connectionName, $this->adaptor);
        $manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
        $manager->setConfiguration([
            'classname'  => 'Propel\\Runtime\\Connection\\ConnectionWrapper',
            'dsn'        => $this->dsn,
            'user'       => $this->user,
            'password'   => $this->password,
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

        $this->insertSql();

        if (!is_null($testData)) {
            $testData->populateDatabase();
        }
    }

    private function insertSql()
    {
        $sqlManager = new \Propel\Generator\Manager\SqlManager();
        $sqlManager->setConnections(
        [$this->connectionName => [
            'dsn'      => $this->dsn,
            'user'     => $this->user,
            'password' => $this->password,
            'adapter'  => $this->adaptor,
        ],
        ]
    );
        $sqlManager->setWorkingDirectory(__DIR__.'/../../generated-sql');
        $sqlManager->insertSql();
    }
}
