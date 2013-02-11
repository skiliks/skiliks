<?php

class m121217_113330_mail_template_reply_all_add extends CDbMigration
{
	public function up()
	{
            $this->alterColumn("mail_template", "type_of_importance", 
                    "enum('none','2_min','plan','info','first_category','spam','reply_all') 
                        CHARACTER SET utf8 COLLATE utf8_general_ci 
                        NULL DEFAULT 'none' 
                        COMMENT 'Is it spam, is it impotrant etc. None - not desided by game autor jet.' 
                        AFTER `sending_date_str`");
	}

	public function down()
	{
            $this->alterColumn("mail_template", "type_of_importance", 
                    "enum('none','2_min','plan','info','first_category','spam') 
                        CHARACTER SET utf8 COLLATE utf8_general_ci 
                        NULL DEFAULT 'none' 
                        COMMENT 'Is it spam, is it impotrant etc. None - not desided by game autor jet.' 
                        AFTER `sending_date_str`");
	}   

}