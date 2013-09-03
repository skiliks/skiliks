<?php

class m130903_152602_add_keep_last_category_after_60_sec_field extends CDbMigration
{
    public function up()
    {
        $this->addColumn('log_activity_action_agregated', 'keep_last_category_after_60_sec', 'TINYINT(20) default null');
    }

    public function down()
    {
        $this->dropColumn('log_activity_action_agregated', 'keep_last_category_after_60_sec');
    }
}