<?php

class m131120_113123_simulation_details_popup extends CDbMigration
{
	public function up()
	{
        $simulations = Simulation::model()->findAll("results_popup_partials_path is not null");
        foreach($simulations as $simulation) {
            /* @var Simulation $simulation */
                $simulation->results_popup_partials_path = '//simulation_details_popup/v1';
                echo $simulation->id."\n";
                $simulation->save(false);
        }
	}

	public function down()
	{
		echo "m131120_113123_simulation_details_popup down.\n";
		return true;
	}
}