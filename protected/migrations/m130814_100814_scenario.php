<?php

class m130814_100814_scenario extends CDbMigration
{
	public function up()
	{

        $this->createTable('scenario_config', [
            'scenario_id' => 'int(11) NOT NULL',
            'import_id' => 'varchar(14) DEFAULT NULL COMMENT "setvice value,used to remove old data after reimport."',
            'game_start_timestamp'=>'varchar(250) not null',
            'game_end_workday_timestamp'=>'varchar(250) not null',
            'game_end_timestamp'=>'varchar(250) not null',
            'game_help_folder_name'=>'varchar(250) not null',
            'game_help_background_jst'=>'varchar(250) not null',
            'game_help_pages'=>'text not null',
            'inbox_folder_icons'=>'text not null',
            'draft_folder_icons'=>'text not null',
            'outbox_folder_icon'=>'text not null',
            'trash_folder_icons'=>'text not null',
            'read_email_screen_icons'=>'text not null',
            'write_new_email_screen_icons'=>'text not null',
            'edit_draft_email_screen_icons'=>'text not null',
            'game_date'=>'varchar(250) not null',
            'intro_video_path'=>'varchar(250) not null',
            'docs_to_save'=>'varchar(250) not null',
            'is_calculate_assessment'=>'varchar(250) not null',
            'is_display_assessment_result_po_up'=>'varchar(250) not null'
        ]);
        $this->addForeignKey('fk_scenario_config_scenario_id', 'scenario_config', 'scenario_id', 'scenario', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_scenario_config_scenario_id', 'scenario_config');
        $this->dropTable('scenario_config');
	}

}