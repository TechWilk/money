<?php

namespace TechWilk\Money;

use TechWilk\Money\Base\HashtagQuery as BaseHashtagQuery;

/**
 * Skeleton subclass for performing query and update operations on the 'hashtag' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class HashtagQuery extends BaseHashtagQuery
{
    public function lastUsedHashtagsForUser($user, $limit = 5)
    {
        return $this
            ->useTransactionHashtagQuery()
                ->useTransactionQuery()
                    ->useAccountQuery()
                        ->filterByUser($user)
                    ->endUse()
                    ->orderById('desc')
                ->endUse()
            ->endUse()
            ->limit($limit)
            ->find();
    }
}
