<?php

class m121221_125302_type_of_init extends CDbMigration
{
	public function up()
	{
        $this->addColumn(
            'dialogs', 
            'type_of_init', 
            'varchar(32) DEFAULT NULL COMMENT \'Replica initialization type: dialog, icon, time, flex etc.\''
        );
	}

	public function down()
	{

	}
}