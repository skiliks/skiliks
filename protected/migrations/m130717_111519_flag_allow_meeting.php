<?php

class m130717_111519_flag_allow_meeting extends CDbMigration
{
	public function up()
	{
        $this->createTable('flag_allow_meeting', [
            'id' => 'pk',
            'flag_code' => 'varchar(10) not null',
            'meeting_id' => 'int not null',
            'value' => 'tinyint(1) default null',
            'import_id' => 'varchar(14) not null',
            'scenario_id' => 'int not null'
        ]);

        //$this->addForeignKey('fk_flag_allow_meeting_flag_code', 'flag_allow_meeting', 'flag_code', 'flag', 'code', 'CASCADE', 'CASCADE');
        //$this->addForeignKey('fk_flag_allow_meeting_meeting_id', 'flag_allow_meeting', 'meeting_id', 'meeting', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropForeignKey('fk_flag_allow_meeting_flag_code', 'flag_allow_meeting');
		$this->dropForeignKey('fk_flag_allow_meeting_meeting_id', 'flag_allow_meeting');

        $this->dropTable('flag_allow_meeting');
	}
}