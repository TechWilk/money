<?php

use Base\Transaction as BaseTransaction;
use Map\TransactionTableMap;

/**
 * Skeleton subclass for representing a row from the 'transaction' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Transaction extends BaseTransaction
{
  /**
  * Set the value of [description] column.
  *
  * @param string $v new value
  * @return $this|\Transaction The current object (for fluent API support)
  */
  public function setDescription($v)
  {
    if ($v !== null) {
        $v = (string) $v;
    }

    if ($this->description !== $v) {
      $this->description = $v;
      $this->modifiedColumns[TransactionTableMap::COL_DESCRIPTION] = true;

      // find and store new hashtags
      $hashtagsAdded;
      preg_match_all("/#(\\w+)/", $v, $hashtags);
      $hashtags = array_map('strtolower', $hashtags[1]);
      foreach ($hashtags as $tag)
      {
        if (HashtagQuery::create()->filterByTag($tag)->filterByTransactionId($this->getId())->count() == 0) // todo: IF hashtag is NOT already set
        {
          $h = new Hashtag();
          $h->setTag($tag);
          $h->setTransaction($this);
          $h->save();
        }
      }

      // remove hashtags no longer in use
      $hashtagsInTransaction = HashtagQuery::create()->filterByTransactionId($this->getId())->find();
      foreach ($hashtagsInTransaction as $tag)
      {
        if (!in_array($tag->getTag(), $hashtags))
        {
          $tag->delete();
        }
      }

    }

    return $this;
  } // setDescription()
}
