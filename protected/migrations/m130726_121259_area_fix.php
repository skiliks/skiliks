<?php

class m130726_121259_area_fix extends CDbMigration
{
	public function up()
	{
        $this->addColumn('learning_goal_group', 'learning_area_code', 'varchar(10) NOT NULL');
        $this->addColumn('learning_goal_group', 'learning_area_id', 'int(11) DEFAULT NULL');
        $this->addForeignKey('fk_simulation_learning_goal_group_learning_area_id', 'learning_goal_group', 'learning_area_id', 'learning_area', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_simulation_learning_goal_group_learning_area_id', 'learning_goal_group');
        $this->dropColumn('learning_goal_group', 'learning_area_code');
        $this->dropColumn('learning_goal_group', 'learning_area_id');
	}

}