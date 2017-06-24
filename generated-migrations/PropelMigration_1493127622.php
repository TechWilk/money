<?php

use Propel\Generator\Manager\MigrationManager;

$path = '/Library/WebServer/Documents/home/money/';

// bootstrap the Propel runtime (and other dependencies)
require_once $path.'vendor/autoload.php';

set_include_path($path.'generated-classes'.PATH_SEPARATOR.get_include_path());
include $path.'generated-conf/config.php';
date_default_timezone_set('UTC');

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1493127622.
 * Generated on 2017-04-25 13:40:22 by user.
 */
class PropelMigration_1493127622
{
    public $comment = '';

    public function preUp(MigrationManager $manager)
    {
        $pdo = $manager->getAdapterConnection('money');
        $sql = 'DELETE FROM hashtag';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    }

    public function postUp(MigrationManager $manager)
    {
        $transactions = TransactionQuery::create()->find();
        foreach ($transactions as $transaction) {
            preg_match_all('/#(\\w+)/', $transaction->getDescription(), $hashtags);
            $hashtags = array_map('strtolower', $hashtags[1]);
            foreach ($hashtags as $tag) {
                $h = new Hashtag();
                if (HashtagQuery::create()->filterByTag($tag)->count() == 0) {
                    $h->setTag($tag);
                    $h->save();
                } else {
                    $h = HashtagQuery::create()->filterByTag($tag)->findOne();
                }
                $transaction->addHashtag($h);
                $transaction->save();
            }
        }
    }

    public function preDown(MigrationManager $manager)
    {
        // add the pre-migration code here
    }

    public function postDown(MigrationManager $manager)
    {
        // add the post-migration code here
    }

    /**
     * Get the SQL statements for the Up migration.
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return [
  'money' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `hashtag` DROP FOREIGN KEY `hashtag_fk_98bea7`;

DROP INDEX `hashtag_fi_98bea7` ON `hashtag`;

ALTER TABLE `hashtag`

  DROP PRIMARY KEY,

  ADD `id` INTEGER NOT NULL AUTO_INCREMENT FIRST,

  DROP `transaction_id`,

  ADD PRIMARY KEY (`id`,`tag`);

CREATE UNIQUE INDEX `hashtag_u_24e05a` ON `hashtag` (`tag`);

CREATE UNIQUE INDEX `hashtag_u_db2f7c` ON `hashtag` (`id`);

CREATE TABLE `transaction_hashtag`
(
    `transaction_id` INTEGER NOT NULL,
    `hashtag_id` INTEGER NOT NULL,
    PRIMARY KEY (`transaction_id`,`hashtag_id`),
    INDEX `transaction_hashtag_fi_8d04e3` (`hashtag_id`),
    CONSTRAINT `transaction_hashtag_fk_98bea7`
        FOREIGN KEY (`transaction_id`)
        REFERENCES `transaction` (`id`),
    CONSTRAINT `transaction_hashtag_fk_8d04e3`
        FOREIGN KEY (`hashtag_id`)
        REFERENCES `hashtag` (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
];
    }

    /**
     * Get the SQL statements for the Down migration.
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return [
  'money' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `transaction_hashtag`;

DROP INDEX `hashtag_u_24e05a` ON `hashtag`;

DROP INDEX `hashtag_u_db2f7c` ON `hashtag`;

ALTER TABLE `hashtag`

  DROP PRIMARY KEY,

  ADD `transaction_id` INTEGER NOT NULL AFTER `tag`,

  DROP `id`,

  ADD PRIMARY KEY (`tag`,`transaction_id`);

CREATE INDEX `hashtag_fi_98bea7` ON `hashtag` (`transaction_id`);

ALTER TABLE `hashtag` ADD CONSTRAINT `hashtag_fk_98bea7`
    FOREIGN KEY (`transaction_id`)
    REFERENCES `transaction` (`id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
];
    }
}
