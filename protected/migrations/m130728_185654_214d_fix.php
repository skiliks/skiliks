<?php

class m130728_185654_214d_fix extends CDbMigration
{
	public function up()
	{
        $this->addColumn('log_activity_action_agregated_214d', 'keep_last_category_initial', 'tinyint(1) default 0');
        $this->addColumn('log_activity_action_agregated_214d', 'keep_last_category_after', 'tinyint(1) default 0');
	}

	public function down()
	{
        $this->dropColumn('log_activity_action_agregated_214d', 'keep_last_category_initial');
        $this->dropColumn('log_activity_action_agregated_214d', 'keep_last_category_after');
	}

}