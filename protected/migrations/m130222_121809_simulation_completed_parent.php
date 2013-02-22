<?php

class m130222_121809_simulation_completed_parent extends CDbMigration
{
	public function up()
	{
        $this->createTable('simulation_completed_parent', [
            'id'=>'pk',
            'sim_id'=>'INT(11) NOT NULL',
            'parent_code'=>'VARCHAR(5) DEFAULT NULL'
        ]);
	}

	public function down()
	{
        $this->dropTable('simulation_completed_parent');
	}

}