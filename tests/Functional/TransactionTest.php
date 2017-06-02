<?php

namespace Tests\Functional;

class TransactionTest extends BaseTestCase
{
    protected $withMiddleware = false;

    /**
     * Test that the hashtags route returns a rendered response containing the text 'All Hashtags' and 'Jump to'
     */
    public function testGetTransactionNew()
    {
        $response = $this->runApp('GET', '/transaction/new');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('New Transaction', (string)$response->getBody());
        $this->assertContains('Save', (string)$response->getBody());
    }

    /**
     * Test that the hashtags route returns a rendered response containing the text 'All Hashtags' and 'Jump to'
     */
    public function testPostTransaction()
    {
        $response = $this->runApp('POST', '/transaction', [ 
            'date' => (new \DateTime())->format('Y-m-d'),
            'value' => 1.05,
            'direction' => 'income',
            'description' => '#something with some #hashtags',
            'account' => [ 1 ],
        ]);

        $this->assertEquals(302, $response->getStatusCode());
    }
}