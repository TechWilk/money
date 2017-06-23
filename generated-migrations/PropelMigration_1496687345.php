<?php

use Propel\Generator\Manager\MigrationManager;

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1496687345.
 * Generated on 2017-06-05 18:29:05 by user.
 */
class PropelMigration_1496687345
{
    public $comment = '';

    public function preUp(MigrationManager $manager)
    {
        // add the pre-migration code here
    }

    public function postUp(MigrationManager $manager)
    {
        // add the post-migration code here
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

DROP INDEX `hashtag_u_db2f7c` ON `hashtag`;

DROP INDEX `tag` ON `hashtag`;

DROP INDEX `tag_2` ON `hashtag`;

ALTER TABLE `user` DROP FOREIGN KEY `user_fk_474870`;

DROP INDEX `user_fi_474870` ON `user`;

ALTER TABLE `user`

  DROP `account_id`;

CREATE TABLE `user_accounts`
(
    `user_id` INTEGER NOT NULL,
    `account_id` INTEGER NOT NULL,
    `alias` VARCHAR(50),
    PRIMARY KEY (`user_id`,`account_id`),
    INDEX `user_accounts_fi_474870` (`account_id`),
    CONSTRAINT `user_accounts_fk_29554a`
        FOREIGN KEY (`user_id`)
        REFERENCES `user` (`id`),
    CONSTRAINT `user_accounts_fk_474870`
        FOREIGN KEY (`account_id`)
        REFERENCES `account` (`id`)
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

DROP TABLE IF EXISTS `user_accounts`;

CREATE UNIQUE INDEX `hashtag_u_db2f7c` ON `hashtag` (`id`);

CREATE UNIQUE INDEX `tag` ON `hashtag` (`tag`);

CREATE UNIQUE INDEX `tag_2` ON `hashtag` (`tag`);

ALTER TABLE `user`

  ADD `account_id` INTEGER NOT NULL AFTER `id`;

CREATE INDEX `user_fi_474870` ON `user` (`account_id`);

ALTER TABLE `user` ADD CONSTRAINT `user_fk_474870`
    FOREIGN KEY (`account_id`)
    REFERENCES `account` (`id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
];
    }
}
