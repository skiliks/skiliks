<?php

class m130114_112601_universal_log extends CDbMigration
{
	public function up()
	{
        $this->createTable('universal_log', array(
              'id'                  => 'pk',
              'sim_id'              => 'int(11) DEFAULT NULL',
              'window_id'           => 'int(11) DEFAULT NULL',
              'mail_id'             => 'int(11) DEFAULT NULL',
              'file_id'             => 'int(11) DEFAULT NULL',
              'dialog_id'           => 'int(11) DEFAULT NULL',
              'last_dialog_id'      => 'int(11) DEFAULT NULL',
              'activity_action_id'  => 'int(11) DEFAULT NULL',
              'start_time'          => 'time NOT NULL',
              'end_time'            => "time NOT NULL DEFAULT '00:00:00'"
        ));
          $this->addForeignKey('universal_log_activity_action_id', 'universal_log', 'activity_action_id', 'activity_action', 'id', 'CASCADE', 'CASCADE');
          $this->addForeignKey('universal_log_dialog_id', 'universal_log', 'dialog_id', 'dialogs', 'id', 'CASCADE', 'CASCADE');
          $this->addForeignKey('universal_log_dialog_last_id', 'universal_log',  'last_dialog_id', 'dialogs', 'id', 'CASCADE', 'CASCADE');
          $this->addForeignKey('universal_log_file_id', 'universal_log', 'file_id', 'my_documents', 'id', 'CASCADE', 'CASCADE');
          $this->addForeignKey('universal_log_mail_id', 'universal_log', 'mail_id', 'mail_box', 'id', 'CASCADE', 'CASCADE');
          $this->addForeignKey('universal_log_window_id', 'universal_log', 'window_id', 'window', 'id', 'CASCADE', 'CASCADE');
          $this->addForeignKey('universal_log_sim_id', 'universal_log', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');

    }

	public function down()
	{
		$this->dropTable('universal_log');
	}

}