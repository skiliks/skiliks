<?php

class m130330_004506_mail_constructor_activity_unique extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('mail_phrases_constructor', 'mail_phrases');
        $this->dropColumn('mail_phrases', 'code');
        $this->dropIndex('PRIMARY', 'mail_constructor');
        $this->addColumn('mail_constructor', 'id', 'pk');
        $this->addColumn('mail_phrases', 'constructor_id', 'int NOT NULL');
        $this->addForeignKey('mail_phrases_constructor', 'mail_phrases', 'constructor_id', 'mail_constructor', 'id');
        $this->dropForeignKey('activity_action_action_id', 'activity_action');
        $this->dropForeignKey('fk_performance_rule_activity_id', 'performance_rule');
        $this->alterColumn('performance_rule', 'activity_id', 'int NOT NULL');
        $this->dropColumn('activity_action', 'activity_id');
        $this->dropIndex('PRIMARY', 'activity');
        $this->renameColumn('activity', 'id', 'code');
        $this->addColumn('activity_action', 'activity_id', 'int not null');
        $this->addColumn('activity', 'id', 'pk');
        $this->addForeignKey('activity_action_activity', 'activity_action', 'activity_id', 'activity', 'id');
        $this->createIndex('activity_uniq', 'activity', 'code, scenario_id', true);
        $this->createIndex('mail_constructor_uniq', 'mail_constructor', 'code, scenario_id', true);
        $this->createIndex('communication_theme_recipient_profix', 'communication_themes', 'character_id, code, mail_prefix');
        $this->dropIndex('activity_action_document_unique', 'activity_action');
        $this->createIndex('activity_action_document_unique', 'activity_action', 'document_id, activity_id, scenario_id');
        $this->dropIndex('activity_action_mail_unique', 'activity_action');
        $this->createIndex('activity_action_mail_unique', 'activity_action', 'mail_id, activity_id, scenario_id');
        $this->dropIndex('mail_code_unique', 'mail_template');
        $this->createIndex('mail_code_unique', 'mail_template', 'code, scenario_id');
	}

	public function down()
	{
		echo "m130330_004506_mail_constructor_activity_unique does not support migration down.\n";
		return false;
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