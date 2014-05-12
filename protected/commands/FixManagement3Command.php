<?php
/**
 * Created by PhpStorm.
 * User: slavka
 * Date: 4/30/14
 * Time: 11:15 AM
 */

class FixManagement3Command extends CConsoleCommand
{
    public function actionIndex($is_fix = 'false') // 7 days
    {
        /** @var Simulation $simulation */
        $simulations = Simulation::model()->findAll(" results_popup_cache is not null
            and scenario_id = 2 and start > '2013-08-01 00:00:00' and status = 'complete' ");

//         $simulations = Simulation::model()->findAll(" id = 5224 ");
//         $simulations = Simulation::model()->findAll(" id = 8802 ");
//           $simulations = Simulation::model()->findAll(" id = 9515 ");
//         $simulations = Simulation::model()->findAll(" id = 6995 ");

        $negative_3_2_behaviours_ids = [];
        $tmpArray_3_2_negative = HeroBehaviour::model()->findAllByAttributes([
            'scenario_id' => 2,
            'code'        => ['3324', '3325'],
        ]);
        foreach ($tmpArray_3_2_negative as $behaviour) {
            $negative_3_2_behaviours_ids[] = $behaviour->id;
        }

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

             // 3.2 {
            $value_3_2_negative_db = AssessmentAggregated::model()->findAllByAttributes([
                'sim_id'   => $simulation->id,
                'point_id' => $negative_3_2_behaviours_ids,
            ]);
            $value_3_2_negative = 0;
            foreach ($value_3_2_negative_db as $value) {
                $value_3_2_negative += abs($value->value);
            }


            if ('//simulation_details_popup/v1' == $simulation->results_popup_partials_path) {
                $new_3_2_negative = number_format($value_3_2_negative*100/4, 2);
                $type = 'v1';
            } else {
                $new_3_2_negative = number_format($value_3_2_negative*100/8, 2);
                $type = 'v2';
            }

            if ($new_3_2_negative != $data['management'][3]['3_2']['-']
                && round($new_3_2_negative, 0) != round($data['management'][3]['3_2']['-'], 0)
                && abs(round($new_3_2_negative, 0) - round($data['management'][3]['3_2']['-'], 0)) > 0.5
            ) {
                echo '| ', $simulation->id, '| 3.2.- ', $type, '|' , $new_3_2_negative,
                ' | ', $data['management'][3]['3_2']['-'],
                ' | ', $simulation->end, ' | ', $simulation->user->profile->email, ' |';
                echo "\n";
                $managerial_3_N++;

                $data['management'][3]['3_2']['-'] = $new_3_2_negative;
            }
            // 3.2 }

            // managerial 3 {
            $value_3_1_positive = ($data['management'][3]['3_1']['+'] / 100) * 6;
            $value_3_2_positive = ($data['management'][3]['3_2']['+'] / 100) * 17;
            $value_3_3_positive = ($data['management'][3]['3_3']['+'] / 100) * 5;
            $value_3_4_positive = ($data['management'][3]['3_4']['+'] / 100) * 7;

            $value_3_4_negative = ($data['management'][3]['3_4']['-']/ 100) * 12.5;

            if ('//simulation_details_popup/v1' == $simulation->results_popup_partials_path) {
                $value_3_2_negative = ($data['management'][3]['3_2']['-'] / 100) * 4;
                $managerial_3_value = (
                        $value_3_1_positive
                        + $value_3_2_positive * (1 - $value_3_2_negative/4)
                        + $value_3_3_positive
                        + $value_3_4_positive * (1 - $value_3_4_negative/12.5)
                    ) / 35;
                $type = 'v1';
            } else {
                $value_3_2_negative = ($data['management'][3]['3_2']['-'] / 100) * 8;
                $managerial_3_value = (
                        $value_3_1_positive
                        + $value_3_2_positive * (1 - $value_3_2_negative/8)
                        + $value_3_3_positive
                        + $value_3_4_positive * (1 - $value_3_4_negative/12.5)
                    ) / 35;
                $type = 'v2';
            }

            $managerial_3_value = round( $managerial_3_value * 100, 2);

            if ($managerial_3_value != $data['management'][3]['total']
                && round($managerial_3_value, 0) != round($data['management'][3]['total'], 0)
                && abs(round($managerial_3_value, 0) - round($data['management'][3]['total'], 0)) > 0.5
            ) {
                echo '| ', $simulation->id, '| 3.х ', $type, '|' , $managerial_3_value,
                ' | ', $data['management'][2]['total'],
                ' | ', $simulation->end, ' | ', $simulation->user->profile->email, ' |';
                echo "\n";
                $managerial_3_N++;

                $data['management'][3]['total'] = $managerial_3_value;
            }
            // managerial 3 }

            // save
            if ('true' == $is_fix) {
                $simulation->results_popup_cache = serialize($data);
                $simulation->save(false);
            }
        }

        echo "\n";
        echo $managerial_3_N;
        echo "\n";
    }
}