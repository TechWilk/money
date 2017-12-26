<?php

namespace TechWilk\Money\Tests\Functional;

use DateTime;

class TransactionsPageTest extends BaseTestCase
{
    protected $withMiddleware = false;

    public function testGetAllTransactions()
    {
        $response = $this->runApp('POST', '/login', ['username' => 'bob@example.com', 'password' => 'really-secure']);
        $response = $this->runApp('GET', '/transactions');

        $monthName = (new DateTime())->format('M Y');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('All Transaction', (string) $response->getBody());
        $this->assertContains('Listed with the newest first', (string) $response->getBody());
        $this->assertContains($monthName, (string) $response->getBody());
        $this->assertContains('a futher  <a href="/tag/test">#test</a> description', (string) $response->getBody());
    }

    public function testGetAllTransactionsForBankAccount()
    {
        $response = $this->runApp('POST', '/login', ['username' => 'bob@example.com', 'password' => 'really-secure']);
        $response = $this->runApp('GET', '/transactions/Bank');

        $monthName = (new DateTime())->format('M Y');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('All Transaction', (string) $response->getBody());
        $this->assertContains('for Bank', (string) $response->getBody());
        $this->assertContains('Listed with the newest first', (string) $response->getBody());
        $this->assertContains($monthName, (string) $response->getBody());
        $this->assertContains('a futher  <a href="/tag/test">#test</a> description', (string) $response->getBody());
    }

    public function testGetAllTransactionsWithHashtag()
    {
        $response = $this->runApp('POST', '/login', ['username' => 'bob@example.com', 'password' => 'really-secure']);
        $response = $this->runApp('GET', '/tag/test');

        $monthName = (new DateTime())->format('M Y');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('All Transaction', (string) $response->getBody());
        $this->assertContains('with #test', (string) $response->getBody());
        $this->assertContains('Listed with the newest first', (string) $response->getBody());
        $this->assertContains($monthName, (string) $response->getBody());
        $this->assertContains('a futher  <a href="/tag/test">#test</a> description', (string) $response->getBody());
    }
}