<?php

class m130201_190413_activity_type extends CDbMigration
{
	public function up()
	{
        $this->createTable('activity_type', [
            'type' => 'VARCHAR(40) CHARACTER SET utf8 NOT NULL PRIMARY KEY '
        ]);
        $this->insert('activity_type', ['type' => 'Documents_leg']);
        $this->insert('activity_type', ['type' => 'Inbox_leg']);
        $this->insert('activity_type', ['type' => 'Manual_dial_leg']);
        $this->insert('activity_type', ['type' => 'Outbox_leg']);
        $this->insert('activity_type', ['type' => 'System_dial_leg']);
        $this->insert('activity_type', ['type' => 'Window']);
        $this->addColumn('activity_action', 'leg_type', 'VARCHAR(40)');
        $this->update('activity_action', ['leg_type' => 'Window'], 'window_id IS NOT NULL');
        $this->update('activity_action', ['leg_type' => 'Inbox_leg'], 'mail_id IS NOT NULL');
        $this->update('activity_action', ['leg_type' => 'Manual_dial_leg'], 'dialog_id IS NOT NULL');
        $this->addForeignKey('activity_action_leg_type', 'activity_action', 'leg_type', 'activity_type', 'type',
            'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropTable('activity_type');
        $this->dropColumn('activity_action', 'leg_type');
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