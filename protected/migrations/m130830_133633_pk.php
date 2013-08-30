<?php

class m130830_133633_pk extends CDbMigration
{
	public function up()
	{
        $this->dropTable("flag_run_email");
        $this->execute("
          CREATE TABLE flag_run_email (
              flag_code varchar(10) NOT NULL,
              mail_code varchar(5) NOT NULL,
              import_id varchar(14) DEFAULT NULL,
              scenario_id int(11) NOT NULL,
              INDEX fk_flag_run_email_flag_code (flag_code),
              INDEX fk_flag_run_email_mail_code (mail_code),
              INDEX flag_run_email_scenario (scenario_id),
              PRIMARY KEY (flag_code, mail_code)
            )
            ENGINE = INNODB
            CHARACTER SET utf8
            COLLATE utf8_general_ci;
        ");
	}

	public function down()
	{
		echo "m130830_133633_pk does not support migration down.\n";
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