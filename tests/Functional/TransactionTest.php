<?php

namespace TechWilk\Money\Tests\Functional;

use TechWilk\Money;

class TransactionTest extends BaseTestCase
{
    protected $withMiddleware = false;

    /**
     * Test that the hashtags route returns a rendered response containing the text 'All Hashtags' and 'Jump to'.
     */
    public function testGetTransactionNew()
    {
        $response = $this->runApp('POST', '/login', ['username' => 'bob@example.com', 'password' => 'really-secure']);
        $response = $this->runApp('GET', '/transaction/new');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('New Transaction', (string) $response->getBody());
        $this->assertContains('Save', (string) $response->getBody());
    }

    public function providerTestPostTransaction()
    {
        return [
      [(new \DateTime())->format('Y-m-d'), 1.05, 'income', '#something with some #hashtags', 1],
      [(new \DateTime('-1 week'))->format('Y-m-d'), 5.89, 'outgoings', '#other #hashtags', 1],
      [(new \DateTime('+2 months'))->format('Y-m-d'), '5.89 + 4.98', 'outgoings', 'I\'ve done some #shopping', 1],
      [(new \DateTime('-10 months'))->format('Y-m-d'), '5*4.99 + 3.97 - 3.1', 'income', 'please do not #error', 1],
    ];
    }

    /**
     * @param string $email
     * @param mixed  $value
     * @param string $direction
     * @param string $description
     * @param int    $account
     *
     * @dataProvider providerTestPostTransaction
     * Test that the hashtags route returns a rendered response containing the text 'All Hashtags' and 'Jump to'
     */
    public function testPostTransaction($date, $value, $direction, $description, $account)
    {
        $response = $this->runApp('POST', '/login', ['username' => 'bob@example.com', 'password' => 'really-secure']);
        $response = $this->runApp('POST', '/transaction', [
            'date'        => $date,
            'value'       => $value,
            'direction'   => $direction,
            'description' => $description,
            'account'     => [$account], // in array, simulating <select>
        ]);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testPostExistingTransaction()
    {
        $response = $this->runApp('POST', '/login', ['username' => 'bob@example.com', 'password' => 'really-secure']);

        $date = (new \DateTime())->format('Y-m-d');
        $value = 5.89;
        $direction = 'outgoings';
        $description = 'not #another description';
        $account = 1;

        $response = $this->runApp('POST', '/transaction/1', [
            'date'        => $date,
            'value'       => $value,
            'direction'   => $direction,
            'description' => $description,
            'account'     => [$account], // in array, simulating <select>
        ]);

        $this->assertEquals(302, $response->getStatusCode());
    }

    public function testGetExistingTransactionDetails()
    {
        $response = $this->runApp('POST', '/login', ['username' => 'bob@example.com', 'password' => 'really-secure']);

        $response = $this->runApp('GET', '/transaction/1');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Transaction', (string) $response->getBody());
        $this->assertContains('edit', (string) $response->getBody());
    }
}
