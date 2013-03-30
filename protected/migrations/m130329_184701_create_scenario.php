<?php

class m130329_184701_create_scenario extends CDbMigration
{
    var $tables = [
        'dialogs', 'replica', 'simulations', 'mail_template', 'flag', 'event_sample', 'activity', 'activity_action',
        'activity_parent', 'characters', 'characters_points', 'communication_themes', 'flag_block_dialog', 'flag_block_mail',
        'flag_block_replica', 'flag_run_email', 'hero_behaviour', 'learning_area', 'learning_goal', 'mail_attachments_template',
        'mail_constructor', 'mail_copies_template', 'mail_phrases', 'mail_points', 'mail_tasks', 'my_documents_template',
        'performance_rule', 'performance_rule_condition', 'tasks'
    ];
	public function up()
	{
        $this->createTable('scenario', [
            'id' => 'pk',
            'name' => 'string',
            'filename' => 'string',
            'slug' => 'string'
        ]);
        $this->createIndex('scenario_slug_unique', 'scenario', 'slug');

        foreach ($this->tables as $table) {
            $this->addColumn($table, 'scenario_id', 'int not null');
            $this->addForeignKey($table . '_scenario', $table, 'scenario_id', 'scenario', 'id', 'cascade', 'cascade');
        }
	}

	public function down()
	{
		echo "m130329_184701_create_scenario does not support migration down.\n";
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