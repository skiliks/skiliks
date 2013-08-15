<?php

class m130815_130449_meeting extends CDbMigration
{
	public function up()
	{
        $this->addColumn('universal_log', 'meeting_id', 'int(11) default null');
        $this->addForeignKey('fk_universal_log_meeting_id', 'universal_log', 'meeting_id', 'meeting', 'id', 'CASCADE', 'CASCADE');
        $this->addColumn('universal_log', 'window_uid', 'varchar(32) DEFAULT NULL');
	}

	public function down()
	{
        $this->dropForeignKey('fk_universal_log_meeting_id', 'universal_log');
        $this->dropColumn('universal_log', 'meeting_id');
        $this->dropColumn('universal_log', 'window_uid');
	}

}