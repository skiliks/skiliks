<?php

class m131010_095350_adding_ip_adress_to_user_table extends CDbMigration
{
	public function up()
	{
        $this->addColumn("user", "ip_address", "varchar (15) DEFAULT NULL");
	}

	public function down()
	{
	    $this->dropColumn("user","ip_address");
    }

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}