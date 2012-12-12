<?php

class m121212_134301_mail_template_update extends CDbMigration
{
	public function up()
	{
        $this->addColumn(
            'mail_template', 
            'type_of_importance', 
            "ENUM('none','2_min','plan','info','first_category') DEFAULT 'none' COMMENT 'Is it spam, is it impotrant etc. None - not desided by game autor jet.'");
	}

	public function down()
	{
        $this->dropColumn('mail_template', 'type_of_importance');
	}

}