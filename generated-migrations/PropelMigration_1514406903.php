<?php

use Propel\Generator\Manager\MigrationManager;

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1514406903.
 * Generated on 2017-12-27 21:35:03 by user.
 */
class PropelMigration_1514406903
{
    public $comment = '';

    public function preUp(MigrationManager $manager)
    {
        // add the pre-migration code here
    }

    public function postUp(MigrationManager $manager)
    {
      $pdo = $manager->getAdapterConnection('money');

      $sql = 'UPDATE transaction t SET
              t.created_by = (SELECT ua.user_id FROM user_accounts ua WHERE ua.account_id = t.account_id LIMIT 1),
              t.created = t.date,
              t.updated = t.date;';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      $sql = 'UPDATE breakdown SET created = NOW(), updated = NOW();';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      $sql = 'UPDATE user SET created = NOW(), updated = NOW();';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();

      $sql = 'UPDATE account SET created = NOW(), updated = NOW();';
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
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

ALTER TABLE `account`

  ADD `created` DATETIME AFTER `name`,

  ADD `updated` DATETIME AFTER `created`;

ALTER TABLE `breakdown`

  ADD `created` DATETIME AFTER `category_id`,

  ADD `updated` DATETIME AFTER `created`;

ALTER TABLE `transaction`

  ADD `created_by` INTEGER NOT NULL AFTER `account_id`,

  ADD `created` DATETIME AFTER `created_by`,

  ADD `updated` DATETIME AFTER `created`;

CREATE INDEX `transaction_fi_c0dfeb` ON `transaction` (`created_by`);

ALTER TABLE `transaction` ADD CONSTRAINT `transaction_fk_c0dfeb`
    FOREIGN KEY (`created_by`)
    REFERENCES `user` (`id`);

ALTER TABLE `user`

  ADD `created` DATETIME AFTER `enable`,

  ADD `updated` DATETIME AFTER `created`;

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

ALTER TABLE `account`

  DROP `created`,

  DROP `updated`;

ALTER TABLE `breakdown`

  DROP `created`,

  DROP `updated`;

ALTER TABLE `transaction` DROP FOREIGN KEY `transaction_fk_c0dfeb`;

DROP INDEX `transaction_fi_c0dfeb` ON `transaction`;

ALTER TABLE `transaction`

  DROP `created_by`,

  DROP `created`,

  DROP `updated`;

ALTER TABLE `user`

  DROP `created`,

  DROP `updated`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
];
    }
}
