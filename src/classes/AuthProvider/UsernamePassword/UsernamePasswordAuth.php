<?php

namespace TechWilk\Money\AuthProvider\UsernamePassword;

use TechWilk\Money\AuthProvider\UsernamePasswordInterface;
use TechWilk\Money\EmailAddress;
use TechWilk\Money\UserQuery;

class UsernamePasswordAuth implements UsernamePasswordInterface
{
    protected $enabled;

    public function __construct($enabled = true)
    {
        $this->enabled = (bool) $enabled;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function checkCredentials($username, $password)
    {
        $email = new EmailAddress($username);

        $users = UserQuery::create()->filterByEmail($email)->find();
        foreach ($users as $u) {
            if ($u->checkPassword($password)) {
                return true;
            }
        }

        return false;
    }

    public function getResetPasswordUrl()
    {
    }

    public function getAuthProviderSlug()
    {
        return 'usernamepassword';
    }
}
