<?php

class m130128_111228_add_learning_goals extends CDbMigration
{
	public function up()
	{
        $this->createTable('learning_goals', [
            'code'      => 'VARCHAR(10) PRIMARY KEY',
            'title'     => 'TEXT',
            'import_id' => 'VARCHAR(14) NOT NULL DEFAULT \'00000000000000\''
        ]);
        
        $this->dropForeignKey('fk_characters_points_titles_parent_id', 'characters_points_titles');
        
        $this->addColumn('characters_points_titles', 'import_id', 'VARCHAR(14) NOT NULL DEFAULT \'00000000000000\' COMMENT \'setvice value,used to remove old data after reimport.\'');
        $this->addColumn('characters_points_titles', 'learning_goal_code', 'VARCHAR(10)');
        $this->dropColumn('characters_points_titles', 'parent_id');
        
        $this->addForeignKey('characters_points_FK_learning_goal_codes', 'characters_points_titles', 'learning_goal_code', 'learning_goals', 'code');
	}

	public function down()
	{
        $this->dropForeignKey('characters_points_FK_learning_goal_id', 'characters_points_titles');
        
		$this->dropTable('learning_goals');
		$this->dropColumn('characters_points_titles', 'import_id');
		$this->dropColumn('characters_points_titles', 'learning_goal_code');
        $this->addColumn('characters_points_titles',  'parent_id', 'INT');
	}
}