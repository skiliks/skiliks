<?php

class m131223_135315_update_template extends CDbMigration
{
	public function up()
	{
        $this->addColumn('mail_box', 'constructor_code', 'varchar(20) default null');
	}

	public function down()
	{
		$this->dropColumn('mail_box', 'constructor_code');
	}

}