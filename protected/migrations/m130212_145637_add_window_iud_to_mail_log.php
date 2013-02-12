<?php

class m130212_145637_add_window_iud_to_mail_log extends CDbMigration
{
    public function up()
    {
        $this->addColumn('log_mail', 'window_uid', "VARCHAR(32) DEFAULT NULL COMMENT 'md5'");
    }

    public function down()
    {
        $this->dropColumn('log_mail', 'window_uid');
    }
}