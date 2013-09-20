<?php

class m130912_082855_payment_system_migration extends CDbMigration
{
	public function up()
	{
        $this->execute('
            DROP TABLE `invoice`;
            CREATE TABLE `invoice` (id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT);
            ALTER TABLE `invoice` ADD `user_id` INT(10) UNSIGNED NULL  DEFAULT NULL  AFTER `id`;
            ALTER TABLE `invoice` ADD `tariff_id` INT(11)  NULL  DEFAULT NULL  AFTER `user_id`;
            ALTER TABLE `invoice` ADD `amount` FLOAT  NULL  DEFAULT NULL  AFTER `tariff_id`;
            ALTER TABLE `invoice` ADD `create_date` INT(11)  NULL  DEFAULT NULL  AFTER `amount`;
            ALTER TABLE `invoice` ADD `paid_date` INT(11)  NULL  DEFAULT NULL  AFTER `create_date`;
            ALTER TABLE `invoice` CHANGE `create_date` `create_date` DATETIME  NULL;
            ALTER TABLE `invoice` ADD `payment_system` INT(11)  NULL  DEFAULT NULL  AFTER `paid_date`;
            ALTER TABLE `invoice` CHANGE `payment_system` `payment_system` VARCHAR(100)  CHARACTER SET utf8  COLLATE utf8_general_ci  NULL  DEFAULT NULL;
            ALTER TABLE `invoice` CHANGE `paid_date` `paid_date` DATETIME  NULL;
            ALTER TABLE `invoice` ADD `additional_data` INT(11)  NULL  DEFAULT NULL  AFTER `payment_system`;
            ALTER TABLE `invoice` ADD `comment` INT(11)  NULL  DEFAULT NULL  AFTER `additional_data`;
            ALTER TABLE `invoice` CHANGE `additional_data` `additional_data` TEXT  NULL;
            ALTER TABLE `invoice` CHANGE `comment` `comment` TEXT  NULL;
            ALTER TABLE `invoice` ADD INDEX `user_ID` (`user_id`);
            ALTER TABLE `invoice` ADD INDEX `tariff_id` (`tariff_id`);
            ALTER TABLE `invoice` ADD CONSTRAINT `user_id_key` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
            ALTER TABLE `invoice` ADD CONSTRAINT `tariff_id_key` FOREIGN KEY (`tariff_id`) REFERENCES `tariff` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
        ');
	}

	public function down()
	{
		echo "m130912_082855_payment_system_migration does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}