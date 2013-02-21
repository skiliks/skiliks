<?php

class m130220_153135_add_usage_to_communication_themes extends CDbMigration
{
	public function up()
	{
        $this->addColumn('communication_themes', 'theme_usage', 'VARCHAR(30) COMMENT \'Representation of Theme_usage\'');
	}

	public function down()
	{
		$this->dropColumn('communication_themes', 'theme_usage');
	}
}