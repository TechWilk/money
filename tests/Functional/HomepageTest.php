<?php

namespace Tests\Functional;

class HomepageTest extends BaseTestCase
{
    protected $withMiddleware = false;

    /**
     * Test that the index route returns a rendered response containing the text 'Dashbpard', 'Totals' and 'view all'
     */
    public function testGetHomepageWithoutName()
    {
        $response = $this->runApp('GET', '/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Dashboard', (string)$response->getBody());
        $this->assertContains('Totals', (string)$response->getBody());
        $this->assertContains('view all', (string)$response->getBody());
        //$this->assertNotContains('Hello', (string)$response->getBody());
    }

    /**
     * Test that the index route won't accept a post request
     */
    public function testPostHomepageNotAllowed()
    {
        $response = $this->runApp('POST', '/', ['test']);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertContains('Method not allowed', (string)$response->getBody());
    }
}