<?php

class m130729_075431_214g extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_assessment_214g', [
            'id'=>'pk',
            'sim_id' => 'int(11) NOT NULL',
            'start_time'=>'time',
            'code' => 'varchar(10)',
            'parent' => 'varchar(10)'
        ]);
        $this->addForeignKey('fk_log_assessment_214g_sim_id', 'log_assessment_214g', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropForeignKey('fk_log_assessment_214g_sim_id', 'log_assessment_214g');
        $this->dropTable('log_assessment_214g');
	}

}