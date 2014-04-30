<?php
/**
 * Created by PhpStorm.
 * User: slavka
 * Date: 4/30/14
 * Time: 11:15 AM
 */

class FixTimeEfficCommand extends CConsoleCommand
{
    public function actionIndex($is_fix = 'false') // 7 days
    {
        $simulations = Simulation::model()->findAll(" results_popup_cache is not null
            and scenario_id = 2 and start > '2013-08-01 00:00:00' and status = 'complete' ");

        echo "\n";

        $efficiencyN = 0;
        $firstPriorityN = 0;
        $nonPriorityN = 0;
        $otherPriorityN = 0;
        $performanceN = 0;
        $managerial_1_N = 0;
        $managerial_2_N = 0;
        $managerial_3_N = 0;

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
//            // проверка
//            if (120 < $data['time']['workday_overhead_duration']) {
//                echo '|',$simulation->id, '| overtime |', '120',
//                '|', $data['time']['workday_overhead_duration'],
//                '|', $simulation->end, '|', $simulation->user->profile->email, '|';
//                echo "\n";
//
//                $data['time']['workday_overhead_duration'] = 120;
//                $fullDuration = 8*60 + 15 + $data['time']['workday_overhead_duration'];
//            }
//
//            if ($fullDuration < $firstPriorityDuration + $nonPriorityDuration) {
//                echo '|',$simulation->id, '| duration |', $fullDuration,
//                '|', ($firstPriorityDuration + $nonPriorityDuration),
//                '|', $simulation->end, '|', $simulation->user->profile->email, '|';
//                echo "\n";
//
//                $fullDuration = $firstPriorityDuration + $nonPriorityDuration;
//            }
//
//            $firstPriorityValue = round(100*$firstPriorityDuration/$fullDuration, 2);
//            $nonPriorityValue = round(100*$nonPriorityDuration/$fullDuration, 2);
//            $otherValue = round(100 - $firstPriorityValue - $nonPriorityValue, 2);
//            if (-0 == $otherValue) {
//                $otherValue = 0;
//            }
//
//            if ($firstPriorityValue != $data['time']['time_spend_for_1st_priority_activities']
//                && round($firstPriorityValue) != $data['time']['time_spend_for_1st_priority_activities']
//                && round($firstPriorityValue) + 1 != $data['time']['time_spend_for_1st_priority_activities']
//                && round($firstPriorityValue) - 1 != $data['time']['time_spend_for_1st_priority_activities']) {
//                echo '|', $simulation->id, '| fistPriority |', $firstPriorityValue,
//                '|', $data['time']['time_spend_for_1st_priority_activities'],
//                '|', $simulation->end, '|', $simulation->user->profile->email, '|';
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
//                echo '|', $simulation->id, '| nonPriority |', $nonPriorityValue,
//                '|', $data['time']['time_spend_for_non_priority_activities'],
//                '|', $simulation->end, '|', $simulation->user->profile->email, '|';
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
//                echo '|', $simulation->id, '| otherPriority |' , $otherValue,
//                '|', $data['time']['time_spend_for_inactivity'],
//                '|', $simulation->end, '|', $simulation->user->profile->email, '|';
//                echo "\n";
//                $otherPriorityN++;
//
//                $data['time']['time_spend_for_inactivity'] = $otherValue;
//            }
            // time_spend }

            // productivity {
//            $productivityZero = 80 * ((isset($data['performance']['0'])) ? $data['performance']['0'] : 0 );
//            $productivityOne  = 35 * ((isset($data['performance']['1'])) ? $data['performance']['1'] : 0 );
//            $productivityTwo  = 19 * ((isset($data['performance']['2'])) ? $data['performance']['2'] : 0 );
//            $productivity2min = 49 * ((isset($data['performance']['2_min'])) ? $data['performance']['2_min'] : 0 );
//
//            $performanceTotalValue = round(
//                ($productivityZero + $productivityOne + $productivityTwo + $productivity2min)/ 183
//            , 2);
//
//            if ($performanceTotalValue != $data['performance']['total'] &&
//                round($performanceTotalValue, 0) != round($data['performance']['total'], 0)) {
//                echo $simulation->id, ': performance total :' , $performanceTotalValue,
//                ',', $data['performance']['total'],
//                ',', $simulation->end, ',', $simulation->user->profile->email;
//                echo "\n";
//                $performanceN++;
//
//                $data['performance']['total'] = $performanceTotalValue;
//            }

            // productivity }

            // managerial 1 {
//            $value_1_1_positive = ($data['management'][1]['1_1']['+'] / 100) * 5.5;
//            $value_1_2_positive = ($data['management'][1]['1_2']['+'] / 100) * 15;
//            $value_1_3_positive = ($data['management'][1]['1_3']['+'] / 100) * 24.5;
//
//            $value_1_1_negative = ($data['management'][1]['1_1']['-'] / 100) * 4;
//            $value_1_2_negative = ($data['management'][1]['1_2']['-'] / 100) * 20;
//            $value_1_3_negative = ($data['management'][1]['1_3']['-']/ 100) * 60 + ($data['management'][1]['1_4']['-'] / 100) * 60;
//
//            if (isset($data['management'][1]['1_5'])) {
//                continue;
//                $value_1_1_positive = ($data['management'][1]['1_2']['+'] / 100) * 5.5;
//                $value_1_2_positive = ($data['management'][1]['1_3']['+'] / 100) * 12;
//                $value_1_3_positive = ($data['management'][1]['1_4']['+'] / 100) * 24.5;
//
//                $value_1_1_negative = ($data['management'][1]['1_2']['-'] / 100) * 4;
//                $value_1_2_negative = ($data['management'][1]['1_3']['-'] / 100) * 20;
//                $value_1_3_negative = ($data['management'][1]['1_4']['-'] / 100) * 60 + ($data['management'][1]['1_5']['-'] / 100) * 60;
//            }
//
//            $managerial_1_value = (
//                    $value_1_1_positive * (1 - $value_1_1_negative/4)
//                    + $value_1_2_positive * (1 - $value_1_2_negative/20)
//                    + $value_1_3_positive * (1 - $value_1_3_negative/120)
//                ) / 45;
//
//            $managerial_1_value = round( $managerial_1_value * 100, 2);
//
//            if ($managerial_1_value != $data['management'][1]['total'] &&
//                round($managerial_1_value, 0) != round($data['management'][1]['total'], 0)
//                // && '2013-12-16 00:00:00' < $simulation->end
//            ) {
//                echo '| ', $simulation->id, '| 1.х |' , $managerial_1_value,
//                ' | ', $data['management'][1]['total'],
//                ' | ', $simulation->end, ' | ', $simulation->user->profile->email, ' |';
//                echo "\n";
//                $managerial_1_N++;
//
//                $data['management'][1]['total'] = $managerial_1_value;
//            }
            // managerial 1 }

            // managerial 2 {
            $value_2_1_positive = ($data['management'][2]['2_1']['+'] / 100) * 12;
            $value_2_2_positive = ($data['management'][2]['2_2']['+'] / 100) * 6;
            $value_2_3_positive = ($data['management'][2]['2_3']['+'] / 100) * 2;

            $value_2_1_negative = ($data['management'][2]['2_1']['-'] / 100) * 28;
            $value_2_2_negative = ($data['management'][2]['2_2']['-'] / 100) * 19;
            $value_2_3_negative = ($data['management'][2]['2_3']['-']/ 100) * 7;

            $managerial_2_value = (
                    $value_2_1_positive * (1 - $value_2_1_negative/28)
                    + $value_2_2_positive * (1 - $value_2_2_negative/19)
                    + $value_2_3_positive * (1 - $value_2_3_negative/7)
                ) / 20;

            $managerial_2_value = round( $managerial_2_value * 100, 2);

            if ($managerial_2_value != $data['management'][2]['total']
                && round($managerial_2_value, 0) != round($data['management'][2]['total'], 0)
                && round($managerial_2_value, 0) + 1 != round($data['management'][2]['total'], 0)
                && round($managerial_2_value, 0) - 1 != round($data['management'][2]['total'], 0)
            ) {
                echo '| ', $simulation->id, '| 2.х |' , $managerial_2_value,
                ' | ', $data['management'][2]['total'],
                ' | ', $simulation->end, ' | ', $simulation->user->profile->email, ' |';
                echo "\n";
                $managerial_2_N++;

                $data['management'][1]['total'] = $managerial_2_value;
            }
            // managerial 2 }

            // managerial 3 {
            $value_3_1_positive = ($data['management'][3]['3_1']['+'] / 100) * 6;
            $value_3_2_positive = ($data['management'][3]['3_2']['+'] / 100) * 17;
            $value_3_3_positive = ($data['management'][3]['3_3']['+'] / 100) * 5;
            $value_3_4_positive = ($data['management'][3]['3_4']['+'] / 100) * 7;

            $value_3_2_negative = ($data['management'][3]['3_2']['-'] / 100) * 8;
            $value_3_4_negative = ($data['management'][3]['3_4']['-']/ 100) * 12.5;

            $managerial_3_value = (
                    $value_3_1_positive
                    + $value_3_2_positive * (1 - $value_3_2_negative/8)
                    + $value_3_3_positive
                    + $value_3_4_positive * (1 - $value_3_4_negative/12.5)
                ) / 35;

            $managerial_3_value = round( $managerial_3_value * 100, 2);

            if ($managerial_3_value != $data['management'][3]['total']
                && round($managerial_3_value, 0) != round($data['management'][3]['total'], 0)
                && round($managerial_3_value, 0) + 1 != round($data['management'][3]['total'], 0)
                && round($managerial_3_value, 0) - 1 != round($data['management'][3]['total'], 0)
            ) {
                echo '| ', $simulation->id, '| 3.х |' , $managerial_3_value,
                ' | ', $data['management'][2]['total'],
                ' | ', $simulation->end, ' | ', $simulation->user->profile->email, ' |';
                echo "\n";
                $managerial_3_N++;

                $data['management'][1]['total'] = $managerial_3_value;
            }
            //     managerial 3 }

            if ('true' == $is_fix) {
            $simulation->results_popup_cache = serialize($data);
            $simulation->save(false);
            }
        }

//        echo $efficiencyN;
//        echo "\n";
//        echo $firstPriorityN;
//        echo "\n";
//        echo $nonPriorityN;
//        echo "\n";
//        echo $otherPriorityN;
//        echo "\n";
//        echo $performanceN;
//        echo "\n";
//        echo $managerial_1_N;
//        echo "\n";
//        echo $managerial_2_N;
//        echo "\n";
        echo $managerial_3_N;
        echo "\n";
    }
}