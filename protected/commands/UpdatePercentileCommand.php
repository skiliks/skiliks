<?php

class UpdatePercentileCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);
        /* @var Simulation[] $simulations */
        $simulations = Simulation::model()->findAll("scenario_id = {$scenario->id} and assessment_version = 'v1' and end is not null and results_popup_cache is not null");
        $count_update = 0;
        foreach($simulations as $simulation) {
            $data = json_decode($simulation->getAssessmentDetails(), true);
            if(false === isset($data['percentile'])) {
                /* @var $assessmentRecord AssessmentOverall */
                $assessmentRecord = AssessmentOverall::model()->findByAttributes([
                    'assessment_category_code' => AssessmentCategory::PERCENTILE,
                    'sim_id'                   => $simulation->id
                ]);
                if($assessmentRecord !== null){
                    $data['percentile']['total'] = $assessmentRecord->value;
                    $simulation->results_popup_cache = serialize($data);
                    $simulation->save(false);
                    $count_update++;
                }
            }
        }

        echo "Simulations update ".$count_update."\r\n";
    }
}