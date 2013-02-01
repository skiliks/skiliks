<?php

class m130201_172458_mail_templates_fk extends CDbMigration
{
	public function safeUp()
	{
        $this->delete('mail_template', 'subject_id NOT IN (SELECT id FROM communication_themes)');
        $this->addForeignKey(
            'mail_template_subject_id',
            'mail_template', 'subject_id', 'communication_themes', 'id', 'CASCADE', 'CASCADE');
	}

	public function safeDown()
	{
		$this->dropForeignKey('mail_template_subject_id', 'mail_template');
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