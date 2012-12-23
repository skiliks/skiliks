<?php

class m121223_105718_fix_for_tables extends CDbMigration
{
	public function up()
	{
        $this->dropTable('activty_efficiency_conditions');
        
        $this->dropTable('events_states'); // безвозвратно
        $this->dropTable('assassment_agregated'); // безвозвратно
        
        $this->createTable(
            'activity_efficiency_conditions', 
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
        
        $this->createTable(
            'assessment_aggregated', 
            array(
                'id'       => "pk",
                'sim_id' => "INT NOT NULL",
                'point_id' => "INT NOT NULL",
                'value'    => "FLOAT",
                )
            );
        $this->createIndex('assessment_aggregated_I_id'      , 'assessment_aggregated', 'id');
        $this->createIndex('assessment_aggregated_I_point_id', 'assessment_aggregated', 'point_id');
        $this->createIndex('assessment_aggregated_I_sim_id'  , 'assessment_aggregated', 'sim_id');
        $this->addForeignKey(
            'assessment_aggregated_FK_character_point_title', 
            'assessment_aggregated',
            'point_id', 
            'characters_points_titles', 
            'id'
        );
        $this->addForeignKey(
            'assessment_aggregated_FK_simulations', 
            'assessment_aggregated',
            'sim_id', 
            'simulations', 
            'id'
        );
	}

	public function down()
	{
        $this->dropTable('activity_efficiency_conditions');
        
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
}