<?php

class m131219_110836_drop_mail_prefix extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('communication_themes_mail_prefix', 'communication_themes');
        $this->dropTable('mail_prefix');
	}

	public function down()
	{
		echo "m131219_110836_drop_mail_prefix migration down.\n";
	}

}