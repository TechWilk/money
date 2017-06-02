<?php

namespace Tests\Functional;

class HashtagsPageTest extends BaseTestCase
{
    protected $withMiddleware = false;

    /**
     * Test that the hashtags route returns a rendered response containing the text 'All Hashtags' and 'Jump to'
     */
    public function testGetHashtagsPage()
    {
        $response = $this->runApp('GET', '/tags');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('All Hashtags', (string)$response->getBody());
        $this->assertContains('Jump to', (string)$response->getBody());
    }

    /**
     * Test that the hashtags route won't accept a post request
     */
    public function testPostHashtagsPageNotAllowed()
    {
        $response = $this->runApp('POST', '/tags', ['test']);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertContains('Method not allowed', (string)$response->getBody());
    }
}