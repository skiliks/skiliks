<?php

class m131122_090152_assessment_version extends CDbMigration
{
	public function up()
	{
        $simulations = Simulation::model()->findAll("results_popup_partials_path is null or assessment_version is null");
        /* @var $simulation Simulation*/
        foreach($simulations as $simulation) {
            $simulation->assessment_version = 'v1';
            $simulation->results_popup_partials_path = '//simulation_details_popup/v1';
            $simulation->save(false);
        }
	}

	public function down()
	{
		echo "m131122_090152_assessment_version does not support migration down.\n";
		return true;
	}
}