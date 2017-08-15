<?php

namespace TechWilk\Money;

use TechWilk\Money\Base\Transaction as BaseTransaction;
use TechWilk\Money\Map\TransactionTableMap;

/**
 * Skeleton subclass for representing a row from the 'transaction' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 */
class Transaction extends BaseTransaction
{
    /**
     * Set the value of [description] column.
     *
     * @param string $v new value
     *
     * @return $this|\Transaction The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = trim($v);
            $this->modifiedColumns[TransactionTableMap::COL_DESCRIPTION] = true;

            // find and store new hashtags
            preg_match_all('/#(\\w+)/', $v, $hashtags);
            $hashtags = array_map('strtolower', $hashtags[1]);
            foreach ($hashtags as $tag) {
                $h = new Hashtag();
                if (HashtagQuery::create()->filterByTag($tag)->count() == 0) {
                    $h->setTag($tag);
                    $h->save();
                } else {
                    $h = HashtagQuery::create()->filterByTag($tag)->findOne();
                }
                $this->addHashtag($h);
            }

            // remove hashtags no longer in use
            foreach ($this->getHashtags() as $tag) {
                if (!in_array($tag->getTag(), $hashtags)) {
                    $this->removeHashtag($tag);
                    $this->save();
                    if ($tag->countTransactions() == 0) {
                        $tag->delete();
                    }
                }
            }
        }

        return $this;
    }

    // setDescription()
}
