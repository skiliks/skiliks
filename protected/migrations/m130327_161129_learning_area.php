<?php

class m130327_161129_learning_area extends CDbMigration
{
	public function up()
	{
        $this->createTable('learning_area', [
            'code' => 'VARCHAR(10) NOT NULL PRIMARY KEY',
            'title' => 'text',
            'import_id' => 'VARCHAR(14)'
        ]);

        $this->addColumn('learning_goal', 'learning_area_code', 'VARCHAR(10)');

        $this->addForeignKey(
            'fk_learning_goal_learning_area_code',
            'learning_goal',
            'learning_area_code',
            'learning_area',
            'code',
            'SET NULL',
            'CASCADE'
        );
	}

	public function down()
	{
		$this->dropForeignKey('fk_learning_goal_learning_area_code', 'learning_goal');
        $this->dropColumn('learning_goal', 'learning_area_code');
        $this->dropTable('learning_area');
	}
}