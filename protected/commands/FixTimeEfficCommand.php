<?php
/**
 * Created by PhpStorm.
 * User: slavka
 * Date: 4/30/14
 * Time: 11:15 AM
 */

class FixTimeEfficCommand extends CConsoleCommand
{
    public function actionIndex() // 7 days
    {
        $simulations = Simulation::model()->findAll(" results_popup_cache is not null
            and scenario_id = 2 and start > '2013-08-01 00:00:00' and status = 'complete' ");

        echo "\n";
        $efficiencyN = 0;
        $firstPriorityN = 0;
        $nonPriorityN = 0;
        $otherPriorityN = 0;
        foreach ($simulations as $simulation) {

            if ('tatiana@skiliks.com' == $simulation->user->profile->email
                || 'tony@skiliks.com' == $simulation->user->profile->email
                || 'vladimir@skiliks.com' == $simulation->user->profile->email
                || 'andrey.sarnavskiy@skiliks.com' == $simulation->user->profile->email) {
                continue;
            }

            $data = unserialize($simulation->results_popup_cache);

            if ($data instanceof stdClass) {
                $data = json_decode(json_encode($data), true);
            }

            // efficiency {
//            $efficiency = $data['time']['time_spend_for_1st_priority_activities']*2/3
//                + ((1 - $data['time']['workday_overhead_duration']/120)*100)/3;
//
//            $efficiency = round($efficiency, 2);
//
//            if ( $efficiency != $data['time']['total'] || $efficiency != $data['time']['efficiency']) {
//                echo $simulation->id, ': efficiency :' ,$efficiency, ',', $data['time']['total'], ',', $data['time']['efficiency'],
//                    ',', $simulation->end, ',', $simulation->user->profile->email;
//                echo "\n";
//                $efficiencyN++;
//
//                $data['time']['total'] = $efficiency;
//                $data['time']['efficiency'] = $efficiency;
//            }
            // efficiency }

            // time_spend {
//            $fullDuration = 8*60 + 15 + $data['time']['workday_overhead_duration'];
//            $firstPriorityDuration = $data['time']['1st_priority_documents']
//                + $data['time']['1st_priority_meetings']
//                + $data['time']['1st_priority_phone_calls']
//                + $data['time']['1st_priority_mail']
//                + $data['time']['1st_priority_planning'];
//            $nonPriorityDuration = $data['time']['non_priority_documents']
//                + $data['time']['non_priority_meetings']
//                + $data['time']['non_priority_phone_calls']
//                + $data['time']['non_priority_mail']
//                + $data['time']['non_priority_planning'];
//
//            $firstPriorityValue = round(100*$firstPriorityDuration/$fullDuration, 2);
//            $nonPriorityValue = round(100*$nonPriorityDuration/$fullDuration, 2);
//            $otherValue = 100 - $firstPriorityValue - $nonPriorityValue;
//
//            if ($firstPriorityValue != $data['time']['time_spend_for_1st_priority_activities']
//                && round($firstPriorityValue) != $data['time']['time_spend_for_1st_priority_activities']
//                && round($firstPriorityValue) + 1 != $data['time']['time_spend_for_1st_priority_activities']
//                && round($firstPriorityValue) - 1 != $data['time']['time_spend_for_1st_priority_activities']) {
//                echo $simulation->id, ': fistPriority :', $firstPriorityValue,
//                ',', $data['time']['time_spend_for_1st_priority_activities'],
//                ',', $simulation->end, ',', $simulation->user->profile->email;
//                echo "\n";
//                $firstPriorityN++;
//
//                $data['time']['time_spend_for_1st_priority_activities'] = $firstPriorityValue;
//            }
//
//            if ($nonPriorityValue != $data['time']['time_spend_for_non_priority_activities']
//                && round($nonPriorityValue) != $data['time']['time_spend_for_non_priority_activities']
//                && round($nonPriorityValue) + 1 != $data['time']['time_spend_for_non_priority_activities']
//                && round($nonPriorityValue) - 1 != $data['time']['time_spend_for_non_priority_activities']) {
//                echo $simulation->id, ': nonPriority :', $nonPriorityValue,
//                ',', $data['time']['time_spend_for_non_priority_activities'],
//                ',', $simulation->end, ',', $simulation->user->profile->email;
//                echo "\n";
//                $nonPriorityN++;
//
//                $data['time']['time_spend_for_non_priority_activities'] = $nonPriorityValue;
//            }
//
//            if ($otherValue != $data['time']['time_spend_for_inactivity']
//                && round($otherValue) != $data['time']['time_spend_for_inactivity']
//                && round($otherValue) + 1 != $data['time']['time_spend_for_inactivity']
//                && round($otherValue) - 1 != $data['time']['time_spend_for_inactivity']) {
//                echo $simulation->id, ': otherPriority :' , $otherValue,
//                ',', $data['time']['time_spend_for_inactivity'],
//                ',', $simulation->end, ',', $simulation->user->profile->email;
//                echo "\n";
//                $otherPriorityN++;
//
//                $data['time']['time_spend_for_inactivity'] = $otherValue;
//            }
            // time_spend }

            // productivity {
                
            // productivity }

//            $simulation->results_popup_cache = serialize($data);
//            $simulation->save(false);
        }

        echo $efficiencyN;
        echo "\n";
        echo $firstPriorityN;
        echo "\n";
        echo $nonPriorityN;
        echo "\n";
        echo $otherPriorityN;
        echo "\n";
    }
}