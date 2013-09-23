<?php

class m130923_090135_payment_log extends CDbMigration
{
	public function up()
	{
        $this->execute('
                         DROP TABLE `log_payment`;
                         CREATE TABLE `log_payment` (id INT(11) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT);
                         ALTER TABLE `log_payment` ADD `invoice_id` INT  NULL  DEFAULT NULL  AFTER `id`;
                         ALTER TABLE `log_payment` ADD `text` TEXT  NULL  AFTER `invoice_id`;
                         ALTER TABLE `log_payment` ADD `created_at` DATETIME  NULL  AFTER `text`;
        ');
	}

	public function down()
	{
		echo "m130923_090135_payment_log does not support migration down.\n";
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