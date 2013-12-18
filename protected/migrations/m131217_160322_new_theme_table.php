<?php

class m131217_160322_new_theme_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('theme',[
            'id' => 'pk',
            'text'=>'varchar (255) default null',
            'theme_code'=>'varchar(10) default null',
            'import_id' => "varchar(14) DEFAULT NULL COMMENT 'setvice value,used to remove old data after reimport.'",
            'scenario_id' => 'int(11) NOT NULL'
        ]);

        $this->addForeignKey('fk_theme_scenario_id', 'theme', 'scenario_id',
            'scenario', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_theme_scenario_id', 'theme');
        $this->dropTable('theme');
	}

}