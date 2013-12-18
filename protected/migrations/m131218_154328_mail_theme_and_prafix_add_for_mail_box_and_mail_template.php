<?php

class m131218_154328_mail_theme_and_prafix_add_for_mail_box_and_mail_template extends CDbMigration
{
	public function up()
	{
        $this->addColumn('mail_template', 'theme_id', 'int(11) DEFAULT NULL');
        $this->addColumn('mail_template', 'mail_prefix', 'varchar(30) DEFAULT NULL');
        $this->addForeignKey('fk_mail_template_theme_id', 'mail_template', 'theme_id',
            'theme', 'id', 'CASCADE', 'CASCADE');

        $this->addColumn('mail_box', 'theme_id', 'int(11) DEFAULT NULL');
        $this->addColumn('mail_box', 'mail_prefix', 'varchar(30) DEFAULT NULL');
        $this->addForeignKey('fk_mail_box_theme_id', 'mail_box', 'theme_id',
            'theme', 'id', 'CASCADE', 'CASCADE');

	}

	public function down()
	{
		$this->dropForeignKey('fk_mail_template_theme_id', 'mail_template');
        $this->dropColumn('mail_template', 'theme_id');
        $this->dropColumn('mail_template', 'mail_prefix');

        $this->dropForeignKey('fk_mail_box_theme_id', 'mail_box');
        $this->dropColumn('mail_box', 'theme_id');
        $this->dropColumn('mail_box', 'mail_prefix');

	}
}