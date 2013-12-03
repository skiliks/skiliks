<?php

class m131018_092843_adding_two_columns_to_invite_for_selenium extends CDbMigration
{
	public function up()
	{
        $this->addColumn("invites","stacktrace", "BLOB");
        $this->addColumn("invites", "is_crashed", "TINYINT (1)");
	}

	public function down()
	{
        $this->dropColumn("invites", "stacktrace");
        $this->dropColumn("invites", "is_crashed");
	}
}