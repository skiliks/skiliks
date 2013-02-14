<?php

class m130214_162545_add_mail_switch_flags extends CDbMigration
{
	public function up()
	{
        $this->addColumn('mail_template', 'flag_to_switch', 'VARCHAR(5)');
	}

	public function down()
	{
        $this->dropColumn('mail_template', 'flag_to_switch');
	}
}