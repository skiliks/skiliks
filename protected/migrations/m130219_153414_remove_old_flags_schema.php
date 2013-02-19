<?php

class m130219_153414_remove_old_flags_schema extends CDbMigration
{
	public function up()
	{
        $this->dropTable('flags_rules_content');
        $this->dropTable('flags_rules');
	}

	public function down()
	{
		echo "m130219_153414_remove_old_flags_schema does not support migration down.\n";
	}
}