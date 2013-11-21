<?php

class m131121_140137_generate_report extends CDbMigration
{
	public function Up()
	{
        $this->addColumn('simulations', 'assessment_version', 'varchar(10) default null');
        $simulations_v1 = Simulation::model()->findAllByAttributes(['results_popup_partials_path' => '//simulation_details_popup/v1']);
        $simulations_v2 = Simulation::model()->findAllByAttributes(['results_popup_partials_path' => '//simulation_details_popup/v2']);

        foreach($simulations_v1 as $simulation) {
            $simulation->assessment_version = 'v1';
            $simulation->save(false);
        }

        foreach($simulations_v2 as $simulation){
            $simulation->assessment_version = 'v2';
            $simulation->save(false);
        }

    }

	public function Down()
	{
        $this->dropColumn('simulations', 'assessment_version');
	}
}