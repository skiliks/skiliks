<?php

/**
 *
 * @author slavka
 */
class m121213_160900_add_type_of_imp extends CDbMigration
{
    public function up()
	{
        $this->alterColumn(
            'mail_template', 
            'type_of_importance', 
            "ENUM('none','2_min','plan','info','first_category','spam') DEFAULT 'none' COMMENT 'Is it spam, is it impotrant etc. None - not desided by game autor jet.'");
	}

	public function down()
	{
        
	}
}

