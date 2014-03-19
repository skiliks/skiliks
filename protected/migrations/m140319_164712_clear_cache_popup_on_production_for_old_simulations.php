<?php

class m140319_164712_clear_cache_popup_on_production_for_old_simulations extends CDbMigration
{
	public function up()
	{
        $sims_id = [1455,4560,4746,5033,5035];

        foreach($sims_id as $sim_id) {

            $simulation = Simulation::model()->findByPk($sim_id);
            if($simulation !== null) {
                /* @var Simulation $simulation */
                $simulation->results_popup_cache = null;
                $simulation->results_popup_partials_path = null;
                $simulation->save(false);
            }

        }
	}

	public function down()
	{

	}

}