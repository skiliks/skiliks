<?php

class m130213_102624_add_window_uid_to_log_activity_action extends CDbMigration
{
    public function up()
    {
        $this->addColumn('log_activity_action', 'window_uid', "VARCHAR(32) DEFAULT NULL COMMENT 'md5'");
    }

    public function down()
    {
        $this->dropColumn('log_activity_action', 'window_uid');
    }
}