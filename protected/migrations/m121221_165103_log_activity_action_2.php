<?php

class m121221_165103_log_activity_action_2 extends CDbMigration
{
    public function up()
    {
        $this->createTable('log_activity_action', array(
            'id' => 'pk',
            'sim_id' => 'integer NOT NULL',
            'window' => 'TINYINT',
            'start_time' => 'TIME NOT NULL',
            'end_time' => 'TIME',
            'activity_action_id' => 'integer'
        ));
        //$this->addForeignKey('activity_action_id', 'log_activity_action', 'activity_action_id', 'activity_action', 'id');
        $this->addForeignKey('log_activity_action_sim_id', 'log_activity_action', 'sim_id', 'simulations', 'id');
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
        $this->dropTable('log_activity_action');
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
