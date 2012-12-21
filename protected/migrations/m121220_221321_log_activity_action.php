<?php

class m121220_221321_log_activity_action extends CDbMigration
{
	public function up()
	{
        /*CREATE TABLE `skiliks`.`<log_activity_action>` (
        `id` integer(11) AUTO_INCREMENT,
	`sim_id` INT(11) NOT NULL,
	`activity_action_id` INT(11) NOT NULL,
	`window` tinyint,
	`start_time` time,
	`end_time` time,
	PRIMARY KEY (`id`),
	FOREIGN KEY (`sim_id`) REFERENCES `skiliks`.`simulations` (`id`)   ON UPDATE CASCADE ON DELETE CASCADE,
	FOREIGN KEY (`activity_action_id`) REFERENCES `skiliks`.`activity_action` (`id`)   ON UPDATE CASCADE ON DELETE CASCADE
) COMMENT='';*/
	}

	public function down()
	{
		echo "m121220_221321_log_activity_action does not support migration down.\n";
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