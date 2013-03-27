<?php

class m130327_130523_evaluation extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulations', 'managerial_skills', 'DECIMAL(10,2) NOT NULL DEFAULT 0');
        $this->addColumn('simulations', 'managerial_productivity', 'DECIMAL(10,2) NOT NULL DEFAULT 0');
        $this->addColumn('simulations', 'time_management_effectiveness', 'DECIMAL(10,2) NOT NULL DEFAULT 0');
        $this->addColumn('simulations', 'overall_manager_rating', 'DECIMAL(10,2) NOT NULL DEFAULT 0');
	}

	public function down()
	{
        $this->dropColumn('simulations', 'managerial_skills');
        $this->dropColumn('simulations', 'managerial_productivity');
        $this->dropColumn('simulations', 'time_management_effectiveness');
        $this->dropColumn('simulations', 'overall_manager_rating');
	}

}