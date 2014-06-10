<?php
/**
 * Created by PhpStorm.
 * User: slavka
 * Date: 4/30/14
 * Time: 11:15 AM
 */

class FixOverallCommand extends CConsoleCommand
{
    public function actionIndex($is_fix = 'false') // 7 days
    {
        /** @var Simulation $simulation */
        $simulations = Simulation::model()->findAll(" results_popup_cache is not null
            and scenario_id = 2 and start > '2013-08-01 00:00:00' and status = 'complete' ");

//        $simulations = Simulation::model()->findAll(" id = 13229 ");

        $overallN = 0;

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

            $data = unserialize($simulation->results_popup_cache);

            if ($data instanceof stdClass) {
                $data = json_decode(json_encode($data), true);
            }

            $newOverall = round(0.5 * $data['management']['total']
                + 0.35 * $data['performance']['total']
                + 0.15 * $data['time']['total'], 2);

            if ($newOverall != $data['overall']
                && abs($newOverall - $data['overall']) > 0.15) {
                echo '|', $simulation->id, '| overall |' , $newOverall,
                '|', $data['overall'],
                '|', $simulation->end, '|', $simulation->user->profile->email, '|';
                echo "\n";
                $overallN++;

                $data['overall'] = $newOverall;
            }

            // save
            if ('true' == $is_fix) {
                $simulation->results_popup_cache = serialize($data);
                $simulation->save(false);
            }
        }

        echo $overallN;
        echo "\n";
    }
}