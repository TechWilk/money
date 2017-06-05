
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- transaction
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `transaction`;

CREATE TABLE `transaction`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `date` DATE NOT NULL,
    `value` FLOAT NOT NULL,
    `description` VARCHAR(100),
    `account_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `transaction_fi_474870` (`account_id`),
    CONSTRAINT `transaction_fk_474870`
        FOREIGN KEY (`account_id`)
        REFERENCES `account` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- breakdown
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `breakdown`;

CREATE TABLE `breakdown`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `transaction_id` INTEGER NOT NULL,
    `description` VARCHAR(50) NOT NULL,
    `value` FLOAT NOT NULL,
    `category_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `breakdown_fi_98bea7` (`transaction_id`),
    INDEX `breakdown_fi_904832` (`category_id`),
    CONSTRAINT `breakdown_fk_98bea7`
        FOREIGN KEY (`transaction_id`)
        REFERENCES `transaction` (`id`),
    CONSTRAINT `breakdown_fk_904832`
        FOREIGN KEY (`category_id`)
        REFERENCES `category` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- category
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `account_id` INTEGER NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `category_fi_474870` (`account_id`),
    CONSTRAINT `category_fk_474870`
        FOREIGN KEY (`account_id`)
        REFERENCES `account` (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- hashtag
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `hashtag`;

CREATE TABLE `hashtag`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `tag` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `hashtag_u_24e05a` (`tag`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- transaction_hashtag
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `transaction_hashtag`;

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

-- ---------------------------------------------------------------------
-- user
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(50) NOT NULL,
    `last_name` VARCHAR(50) NOT NULL,
    `email` VARCHAR(50) NOT NULL,
    `password_hash` VARCHAR(80) NOT NULL,
    `password_expire` DATETIME,
    `enable` TINYINT(1) DEFAULT 1 NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- user_accounts
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `user_accounts`;

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

-- ---------------------------------------------------------------------
-- account
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `account`;

CREATE TABLE `account`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- loginFailure
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `loginFailure`;

CREATE TABLE `loginFailure`
(
    `username` VARCHAR(30) NOT NULL,
    `ipAddress` VARCHAR(15) NOT NULL,
    `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
