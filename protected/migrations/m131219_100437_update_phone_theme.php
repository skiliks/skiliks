<?php

class m131219_100437_update_phone_theme extends CDbMigration
{
	public function up()
	{
        $this->addColumn('outgoing_phone_themes', 'phone_dialog_number', 'varchar(10) default null');

	}

	public function down()
	{
        $this->dropColumn('outgoing_phone_themes', 'phone_dialog_number');
	}

}