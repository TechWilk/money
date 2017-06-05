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


    public function testCreateUserForTests()
    {
        $a = \AccountQuery::create()->findPk(1);

        $u = new \User();
        $u->setFirstName('Tim');
        $u->setLastName('Smith');
        $u->setEmail(new \EmailAddress('tim@example.com'));
        $u->setPassword('MegaSecurePassword');
        $u->addAccount($a);
        $u->setEnable(true);

        $this->assertTrue($u->save() > 0);

        return $u->getId();
    }

    /**
     * @depends testCreateUserForTests
     */
    public function testGetUserPageForTimSmith($userId)
    {
        $response = $this->runApp('GET', '/user/'.$userId);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Tim Smith', (string)$response->getBody());
        //$this->assertNotContains('Hello', (string)$response->getBody());
    }

    /**
     * @depends testCreateUserForTests
     */
    public function testPostUserPasswordChangeIncorrectOldPassword($userId)
    {
        $response = $this->runApp('POST', '/user/'.$userId.'/password', [ 'old' => 'wrong', 'new' => 'newSecurePassword', 'confirm' => 'newSecurePassword' ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Tim Smith', (string)$response->getBody());
        $this->assertContains('Old password incorrect', (string)$response->getBody());
        $this->assertNotContains('Changed successfully', (string)$response->getBody());
    }

    /**
     * @depends testCreateUserForTests
     */
    public function testPostUserPasswordChangeNewPasswordsNotMatch($userId)
    {
        $response = $this->runApp('POST', '/user/'.$userId.'/password', [ 'old' => 'MegaSecurePassword', 'new' => 'newSecurePassword', 'confirm' => 'differentNewPassword' ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Tim Smith', (string)$response->getBody());
        $this->assertContains('New passwords do not match', (string)$response->getBody());
        $this->assertNotContains('Changed successfully', (string)$response->getBody());
    }

    /**
     * @depends testCreateUserForTests
     */
    public function testPostUserPasswordChangeNewPasswordsTooShort($userId)
    {
        $response = $this->runApp('POST', '/user/'.$userId.'/password', [ 'old' => 'MegaSecurePassword', 'new' => 'short', 'confirm' => 'short' ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Tim Smith', (string)$response->getBody());
        $this->assertContains('too short', (string)$response->getBody());
        $this->assertNotContains('Changed successfully', (string)$response->getBody());
    }

    /**
     * @depends testCreateUserForTests
     */
    public function testPostUserPasswordChangeNewPasswordSuccessful($userId)
    {
        $response = $this->runApp('POST', '/user/'.$userId.'/password', [ 'old' => 'MegaSecurePassword', 'new' => 'newSecurePassword', 'confirm' => 'newSecurePassword' ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertContains('Tim Smith', (string)$response->getBody());
        $this->assertContains('Changed successfully', (string)$response->getBody());

        $u = \UserQuery::create()->findPk($userId);
        $this->assertTrue($u->checkPassword('newSecurePassword'));
    }
}