<?php

namespace TechWilk\Money;

use TechWilk\Money\Base\User as BaseUser;
use TechWilk\Money\Map\UserTableMap;

/**
 * Skeleton subclass for representing a row from the 'user' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class User extends BaseUser
{
    /**
     * Set the value of [email] column.
     *
     * @param \EmailAddress $v new value
     *
     * @return $this|\User The current object (for fluent API support)
     */
    public function setEmail(EmailAddress $v)
    {
        if ($this->email !== $v) {
            $this->email = $v;
            $this->modifiedColumns[UserTableMap::COL_EMAIL] = true;
        }

        return $this;
    }

    // setEmail()

    /**
     * Set the value of [password_hash] column.
     *
     * @param string $v new value
     *
     * @return $this|\User The current object (for fluent API support)
     */
    public function setPassword($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if (!password_verify($v, $this->password_hash)) {
            $bcrypt_options = [
                'cost' => 12,
            ];
            $this->password_hash = password_hash($v, PASSWORD_BCRYPT, $bcrypt_options);
            $this->modifiedColumns[UserTableMap::COL_PASSWORD_HASH] = true;
        }

        return $this;
    }

    // setPassword()

    /**
     * Check a plain text password against the value of [password_hash] column.
     *
     * @param string $v plain text password
     *
     * @return bool Whether password is correct
     */
    public function checkPassword($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        } else {
            return false;
        }

        return password_verify($v, $this->password_hash);
    }

    // checkPassword()

    /**
     * Get the [firstname] and [lastname] column value concatenated with a space.
     *
     * @return string
     */
    public function getName()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
