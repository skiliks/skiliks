<?php

class m130221_104334_parent_activity_ending extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('activity_parent', 'id', 'INT(11) AUTO_INCREMENT');
        $this->addColumn('activity_parent', 'parent_code', 'VARCHAR(10) NOT NULL');
        $this->addColumn('activity_parent', 'dialog_id', 'integer');
        $this->addColumn('activity_parent', 'mail_id', 'integer');
	}

	public function down()
	{
        $this->alterColumn('activity_parent', 'id', 'VARCHAR(10) NOT NULL');
        $this->dropColumn('activity_parent', 'parent_code');
        $this->dropColumn('activity_parent', 'dialog_id');
        $this->dropColumn('activity_parent', 'mail_id');
	}
}