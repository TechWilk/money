<?php

namespace TechWilk\Money;

use Psr\Container\ContainerInterface;
use TechWilk\Money\Base\AccountQuery as BaseAccountQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'account' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class AccountQuery extends BaseAccountQuery
{
    public function filterByCurrentUser(ContainerInterface $container)
    {
        $auth = new Authentication($container);

        return $this->filterByUser($auth->currentUser());
    }
}
