<?php

class m131122_090152_assessment_version extends CDbMigration
{
	public function up()
	{
        $simulations_v1 = $this->dbConnection->createCommand()
            ->select('id')
            ->from("simulations")
            ->where("results_popup_partials_path = '//simulation_details_popup/v1'")
            ->queryAll();
        //Simulation::model()->findAllByAttributes(['results_popup_partials_path' => '//simulation_details_popup/v1']);
        $simulations_v2 = $this->dbConnection->createCommand()
            ->select('id')
            ->from("simulations")
            ->where("results_popup_partials_path = '//simulation_details_popup/v2'")
            ->queryAll(); //Simulation::model()->findAllByAttributes(['results_popup_partials_path' => '//simulation_details_popup/v2']);

        foreach($simulations_v1 as $simulation) {
            $this->update('simulations', ['assessment_version'=>'v1'], 'id = :id', ['id'=>$simulation['id']]);
            //$simulation->assessment_version = 'v1';
            //$simulation->save(false);
        }

        foreach($simulations_v2 as $simulation) {
            $this->update('simulations', ['assessment_version'=>'v2'], 'id = :id', ['id'=>$simulation['id']]);
            //$simulation->assessment_version = 'v2';
            //$simulation->save(false);
        }
        $simulations = $this->dbConnection->createCommand()
            ->select('id')
            ->from("simulations")
            ->where("results_popup_partials_path is null or assessment_version is null")
            ->queryAll();
            //Simulation::model()->findAll("results_popup_partials_path is null or assessment_version is null");
        /* @var $simulation Simulation*/
        foreach($simulations as $simulation) {
            $this->update('simulations',
                ['assessment_version'=>'v1',
                 'results_popup_partials_path' => '//simulation_details_popup/v1'
                ], 'id = :id', ['id'=>$simulation['id']]);
//            $simulation->assessment_version = 'v1';
//            $simulation->results_popup_partials_path = '//simulation_details_popup/v1';
//            $simulation->save(false);
        }
	}

	public function down()
	{
		echo "m131122_090152_assessment_version does not support migration down.\n";
		return true;
	}
}