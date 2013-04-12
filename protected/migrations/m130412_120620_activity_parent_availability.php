<?php

class m130412_120620_activity_parent_availability extends CDbMigration
{
	public function up()
	{
        $this->createTable('activity_parent_availability', [
            'id'           => 'pk',
            'code'         => 'VARCHAR (10) NOT NULL',
            'category'     => 'VARCHAR (10) NOT NULL',
            'available_at' => 'TIME NOT NULL',
            'scenario_id'  => 'INT',
            'import_id'    => 'VARCHAR(14)',
        ]);

        $this->addForeignKey(
            'activity_parent_availability_fk_scenario',
            'activity_parent_availability',
            'scenario_id',
            'scenario',
            'id',
            'CASCADE',
            'CASCADE'
        );
	}

	public function down()
	{
        $this->dropTable('activity_parent_availability');
	}
}