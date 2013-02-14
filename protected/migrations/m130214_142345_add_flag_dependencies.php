<?php

class m130214_142345_add_flag_dependencies extends CDbMigration
{
	public function up()
	{
        $this->createTable('flag_run_email', array(
            'flag_code' => 'VARCHAR(10) NOT NULL',
            'mail_code' => 'VARCHAR(5) NOT NULL',
            'import_id' => 'VARCHAR(14)',
        ));

        $this->createTable('flag_block_replica', array(
            'flag_code'  => 'VARCHAR(5) NOT NULL',
            'replica_id' => 'INT(11) NOT NULL',
            'value'      => 'boolean',
            'import_id'  => 'VARCHAR(14)',
        ));

        $this->addForeignKey('fk_flag_run_email_flag_code', 'flag_run_email', 'flag_code', 'flag', 'code', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_flag_run_email_mail_code', 'flag_run_email', 'mail_code', 'mail_template', 'code', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk_flag_block_replica_flag_code', 'flag_block_replica', 'flag_code', 'flag', 'code', 'CASCADE', 'CASCADE');
        //$this->addForeignKey('fk_flag_block_replica_replica_id', 'flag_block_replica', 'replica_id', 'dialogs', 'excel_id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('flag_run_email');
		$this->dropTable('flag_block_replica');
	}
}