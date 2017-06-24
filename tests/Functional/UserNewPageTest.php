<?php

namespace TechWilk\Money\Tests\Functional;

use TechWilk\Money;

class UserNewPageTest extends BaseTestCase
{
    protected $withMiddleware = false;

    /**
     * Test that the index route returns a rendered response containing the text 'Dashbpard', 'Totals' and 'view all'.
     */
    public function testGetUserNewPage()
    {
        $response = $this->runApp('GET', '/user/new');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Create user', (string) $response->getBody());
        //$this->assertNotContains('Hello', (string)$response->getBody());
    }

    public function testCreateUserPasswordTooShort()
    {
        $response = $this->runApp('POST', '/user', [
            'first-name'       => 'Terry',
            'last-name'        => 'Harris',
            'email'            => 'terry@example.com',
            'password'         => 'short',
            'password-confirm' => 'short',
        ]);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertContains('too short', (string) $response->getBody());
    }

    public function testCreateUserPasswordsNotMatch()
    {
        $response = $this->runApp('POST', '/user', [
            'first-name'       => 'Terry',
            'last-name'        => 'Harris',
            'email'            => 'terry@example.com',
            'password'         => 'ThisIsNotVerySecure',
            'password-confirm' => 'ChooseABetterPassword',
        ]);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertContains('Passwords do not match', (string) $response->getBody());
    }

    public function testCreateUserSuccess()
    {
        $response = $this->runApp('POST', '/user', [
            'first-name'       => 'Terry',
            'last-name'        => 'Harris',
            'email'            => 'terry@example.com',
            'password'         => 'ChooseABetterPassword',
            'password-confirm' => 'ChooseABetterPassword',
        ]);

        $this->assertEquals(303, $response->getStatusCode());
        //$this->assertContains('Passwords do not match', (string)$response->getBody());
    }
}
