<?php

class m130218_205136_flag_block_mail extends CDbMigration
{
	public function up()
	{
        $this->createTable('flag_block_mail', array(
            'id' => 'pk',
            'flag_code' => 'VARCHAR(5) CHARSET utf8 NOT NULL',
            'value' => 'boolean',
            'mail_template_id' => 'INT(11) NOT NULL',
            'import_id' => 'varchar(60)'
        ));
        $this->addForeignKey(
            'flag_block_mail_mail_template',
            'flag_block_mail', 'mail_template_id', 'mail_template', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey(
            'fk_flag_block_mail__flag_code',
            'flag_block_mail',
            'flag_code',
            'flag',
            'code',
            'CASCADE',
            'CASCADE'
        );
	}

	public function down()
	{
        $this->dropTable('flag_block_mail');

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