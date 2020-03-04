<?php

namespace TechWilk\Money\Tests\Functional;

class HashtagsPageTest extends BaseTestCase
{
    protected $withMiddleware = false;

    /**
     * Test that the hashtags route returns a rendered response containing the text 'All Hashtags' and 'Jump to'.
     */
    public function testGetHashtagsPage()
    {
        $response = $this->runApp('POST', '/login', ['username' => 'bob@example.com', 'password' => 'really-secure']);
        $response = $this->runApp('GET', '/tags');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('All Hashtags', (string) $response->getBody());
        $this->assertContains('Jump to', (string) $response->getBody());
    }

    /**
     * Test that the hashtags route won't accept a post request.
     */
    public function testPostHashtagsPageNotAllowed()
    {
        $response = $this->runApp('POST', '/tags', ['test']);

        $this->assertEquals(405, $response->getStatusCode());
        $this->assertContains('Method not allowed', (string) $response->getBody());
    }

    /**
     * Test that the hashtags route returns a rendered response containing the text 'All Hashtags' and 'Jump to'.
     */
    public function testGetHashtagsJsonAll()
    {
        $response = $this->runApp('POST', '/login', ['username' => 'bob@example.com', 'password' => 'really-secure']);
        $response = $this->runApp('GET', '/tags.json');

        $this->assertEquals(200, $response->getStatusCode());

        $hashtags = json_decode((string) $response->getBody(), true);

        $this->assertContains(['test', 2], $hashtags);
        $this->assertContains(['something', 1], $hashtags);
        $this->assertContains(['different', 4], $hashtags);
    }

    public function providerTestGetHashtagsJsonWithQueryParameter()
    {
        return [
            ['test', [['test', 2]]],
            ['te', [['test', 2]]],
            ['t', [['test', 2], ['something', 1], ['different', 4]]],
        ];
    }

    /**
     * @param string $email
     * @param string $email
     *
     * @dataProvider providerTestGetHashtagsJsonWithQueryParameter
     */
    public function testGetHashtagsJsonWithQueryParameter($q, $expected)
    {
        $response = $this->runApp('POST', '/login', ['username' => 'bob@example.com', 'password' => 'really-secure']);
        $response = $this->runApp('GET', '/tags.json', ['q' => $q]);

        $this->assertEquals(200, $response->getStatusCode());

        $hashtags = json_decode((string) $response->getBody(), true);

        foreach ($expected as $contents) {
            $this->assertContains($contents, $hashtags);
        }
    }

    public function testGetHashtagsJsonWithInvalidQueryParameter()
    {
        $response = $this->runApp('POST', '/login', ['username' => 'bob@example.com', 'password' => 'really-secure']);
        $response = $this->runApp('GET', '/tags.json', ['q' => 'ubadfuip32hafnidfo']);

        $this->assertEquals(200, $response->getStatusCode());

        $hashtags = json_decode((string) $response->getBody(), true);

        $this->assertEmpty($hashtags);
    }
}
