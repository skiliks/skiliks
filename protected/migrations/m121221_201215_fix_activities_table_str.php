<?php

class m121221_201215_fix_activities_table_str extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('activity', 'is_keep_last_category');
        $this->addColumn('activity_action', 'is_keep_last_category','TINYINT (1) DEFAULT 0');
        $this->addColumn('activity', 'type','VARCHAR(20) COMMENT \'Task ot Activity. Task is important thihg, activiti - trash.\'');
        
        $this->createTable(
            'activty_efficiency_conditions', 
            array(
                'id'                   => 'pk',
                'activity_id'          => 'VARCHAR(10) DEFAULT NULL',
                'type'                 => 'VARCHAR(30) DEFAULT NULL',
                'result_code'          => 'VARCHAR(30) DEFAULT NULL',
                'email_template_id'    => 'INT DEFAULT NULL',
                'dialog_id'            => 'INT DEFAULT NULL',
                'operation'            => 'VARCHAR(5) DEFAULT NULL',
                'efficiency_value'     => 'INT DEFAULT NULL',
                'fail_less_coeficient' => 'VARCHAR(5) DEFAULT NULL',
                'import_id'            => 'VARCHAR(14)',
        ));
	}

	public function down()
	{
        $this->dropColumn('activity_action', 'is_keep_last_category');
        $this->addColumn('activity', 'is_keep_last_category','TINYINT (1) DEFAULT 0');
        $this->dropColumn('activity', 'type');
        $this->dropTable('activty_efficiency_conditions');
	}
}