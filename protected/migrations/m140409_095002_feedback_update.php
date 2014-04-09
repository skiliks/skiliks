<?php

class m140409_095002_feedback_update extends CDbMigration
{
	public function up()
	{
        $this->addColumn('feedback', 'is_processed', 'tinyint(1) default 0');
        $this->addColumn('feedback', 'comment', 'text default null');
	}

	public function down()
	{

	}

}