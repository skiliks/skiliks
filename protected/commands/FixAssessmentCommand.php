<?php

class FixAssessmentCommand extends CConsoleCommand {
    public function actionIndex($isFix = false)
    {
        ini_set('max_execution_time', 60*60*0.5); // 30 min
        ini_set('memory_limit', '-1');

        $simulations = Simulation::model()->findAll(
            " results_popup_cache is not null
            and scenario_id = 2
            and start > '2013-08-01 00:00:00'
            and status = 'complete' "
        );

        // симуляций меньше 1000, можно и циклом пройтись
        $userSimulations = [];
        foreach ($simulations as $simulation) {
            if ('tatiana@skiliks.com' == $simulation->user->profile->email
                || 'tony@skiliks.com' == $simulation->user->profile->email
                || 'vladimir@skiliks.com' == $simulation->user->profile->email
                || 'vladimir1@skiliks.com' == $simulation->user->profile->email
                || 'tetyana.grybok@skiliks.com' == $simulation->user->profile->email
                || 'sarnavskyi89@gmail.com' == $simulation->user->profile->email
                || 'andrey.sarnavskiy@skiliks.com' == $simulation->user->profile->email) {
                continue;
            }
            $userSimulations[] = $simulation;
        }

        unset($simulations);

        /**
         * @var CheckAssessmentResults $component
         */
        $component = new CheckAssessmentResults();
        $component->mode = CheckAssessmentResults::MODE_CONSOLE;
        $component->checkAndFix($userSimulations, (bool)$isFix);
    }
} 