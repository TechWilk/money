<?php

namespace TechWilk\Money;

use TechWilk\Money\Base\TransactionQuery as BaseTransactionQuery;
use Psr\Container\ContainerInterface;

/**
 * Skeleton subclass for performing query and update operations on the 'transaction' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class TransactionQuery extends BaseTransactionQuery
{
    public function forCurrentUser(ContainerInterface $container)
    {
        $auth = new Authentication($container);

        return $this->useAccountQuery()->filterByUser($auth->currentUser())->endUse();
    }
}
