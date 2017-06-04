<?php

namespace Tests\Functional;

class UserPageTest extends BaseTestCase
{
    protected $withMiddleware = false;

    /**
     * Test that the index route returns a rendered response containing the text 'Dashbpard', 'Totals' and 'view all'
     */
    public function testGetUserPage()
    {
        $response = $this->runApp('GET', '/user/1');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Bob Jones', (string)$response->getBody());
        //$this->assertNotContains('Hello', (string)$response->getBody());
    }
}