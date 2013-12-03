<?php

class m131107_121642_update_replicas_for_media extends CDbMigration
{
	public function up()
	{
        $this->renameColumn('replica', 'sound', 'media_file_name');
        $this->addColumn('replica', 'media_type', 'varchar(20) default null after media_file_name');
	}

	public function down()
	{
        $this->renameColumn('replica', 'media_file_name', 'sound');
        $this->dropColumn('replica', 'media_type');
	}
}