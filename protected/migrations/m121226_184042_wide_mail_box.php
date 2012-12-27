<?php

class m121226_184042_wide_mail_box extends CDbMigration
{
	public function up()
	{
        $this->addColumn(
            'mail_box', 
            'coincidence_type', 
            "VARCHAR(25) DEFAULT NULL COMMENT 'full/part1/part2, null - no coincidence'");
        $this->addColumn(
            'mail_box', 
            'coincidence_mail_code', 
            "VARCHAR(5) DEFAULT NULL COMMENT 'Like MS1, MS2 etc., null - no coincidence'");
	}

	public function down()
	{
        $this->dropColumn('mail_box', 'coincidence_type');
        $this->dropColumn('mail_box', 'coincidence_mail_code');
	}
}