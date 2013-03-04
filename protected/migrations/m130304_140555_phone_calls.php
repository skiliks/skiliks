<?php

class m130304_140555_phone_calls extends CDbMigration
{
	public function up()
	{
        $this->addColumn('phone_calls', 'theme_id', 'INT(11) DEFAULT NULL');
	    $this->addForeignKey('phone_calls_theme_id', 'phone_calls', 'theme_id', 'communication_themes', 'id', 'CASCADE', 'CASCADE');
    }

	public function down()
	{
        $this->dropColumn('phone_calls', 'theme_id');
        $this->dropForeignKey('phone_calls_theme_id', 'phone_calls');
	}

}