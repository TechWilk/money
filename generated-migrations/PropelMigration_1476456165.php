<?php

use Propel\Generator\Manager\MigrationManager;

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1476456165.
 * Generated on 2016-10-14 14:42:45 by user
 */
class PropelMigration_1476456165
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
     * Get the SQL statements for the Up migration
     *
     * @return array list of the SQL strings to execute for the Up migration
     *               the keys being the datasources
     */
    public function getUpSQL()
    {
        return array (
  'money' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `breakdown`

  ADD `transaction_id` INTEGER NOT NULL AFTER `id`;

CREATE INDEX `breakdown_fi_98bea7` ON `breakdown` (`transaction_id`);

ALTER TABLE `breakdown` ADD CONSTRAINT `breakdown_fk_98bea7`
    FOREIGN KEY (`transaction_id`)
    REFERENCES `transaction` (`id`);

ALTER TABLE `category` DROP FOREIGN KEY `category_fk_98bea7`;

DROP INDEX `category_fi_98bea7` ON `category`;

ALTER TABLE `category`

  DROP `transaction_id`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

    /**
     * Get the SQL statements for the Down migration
     *
     * @return array list of the SQL strings to execute for the Down migration
     *               the keys being the datasources
     */
    public function getDownSQL()
    {
        return array (
  'money' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `breakdown` DROP FOREIGN KEY `breakdown_fk_98bea7`;

DROP INDEX `breakdown_fi_98bea7` ON `breakdown`;

ALTER TABLE `breakdown`

  DROP `transaction_id`;

ALTER TABLE `category`

  ADD `transaction_id` INTEGER NOT NULL AFTER `id`;

CREATE INDEX `category_fi_98bea7` ON `category` (`transaction_id`);

ALTER TABLE `category` ADD CONSTRAINT `category_fk_98bea7`
    FOREIGN KEY (`transaction_id`)
    REFERENCES `transaction` (`id`);

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
);
    }

}