<?php
/**
 * Created by PhpStorm.
 * User: slavka
 * Date: 4/30/14
 * Time: 11:15 AM
 */

class FixManagement1totalCommand extends CConsoleCommand
{
    public function actionIndex($is_fix = 'false') // 7 days
    {
        /** @var Simulation $simulation */
        $simulations = Simulation::model()->findAll(" results_popup_cache is not null
            and scenario_id = 2 and start > '2013-08-01 00:00:00' and status = 'complete' ");

//         $simulations = Simulation::model()->findAll(" id = 5224 ");
//         $simulations = Simulation::model()->findAll(" id = 8802 ");
//           $simulations = Simulation::model()->findAll(" id = 9515 ");
//         $simulations = Simulation::model()->findAll(" id = 13229 ");
//         $simulations = Simulation::model()->findAll(" id = 4995 ");
         $simulations = Simulation::model()->findAll(" id in(4995, 4997, 5009, 9508)  ");

        $negative_1_3_behaviours_ids = [];
        $negative_1_4_behaviours_ids = [];

        $tmpArrayD = HeroBehaviour::model()->findAllByAttributes([
            'scenario_id' => 2,
            'code'        => ['214d5', '214d6', '214d8'],
        ]);
        $tmpArrayG = HeroBehaviour::model()->findAllByAttributes([
            'scenario_id' => 2,
            'code'        => ['214g0', '214g1'],
        ]);

        foreach ($tmpArrayD as $behaviour) {
            $negative_1_3_behaviours_ids[] = $behaviour->id;
        }

        foreach ($tmpArrayG as $behaviour) {
            $negative_1_4_behaviours_ids[] = $behaviour->id;
        }

        $managerial_1_N = 0;

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

            $value_1_3_negative_db = AssessmentAggregated::model()->findAllByAttributes([
                'sim_id'   => $simulation->id,
                'point_id' => array_merge($negative_1_3_behaviours_ids, $negative_1_4_behaviours_ids),
            ]);
            $value_1_3_negative = 0;

            foreach ($value_1_3_negative_db as $value) {
                $value_1_3_negative += abs($value->value);
            }

            if ('//simulation_details_popup/v1' == $simulation->results_popup_partials_path) {

                // V1 {

                $value_1_3_negative = $value_1_3_negative/120; // 60 за 1.3 плюс 60 за 1.4

                if (1 < $value_1_3_negative) {
                    $value_1_3_negative = 1;
                }

                $part1 = round(($data['management'][1]['1_2']['+']/100)*5.5*(1 - $data['management'][1]['1_2']['-']/100)
                    + ($data['management'][1]['1_3']['+']/100)*15*(1 - $data['management'][1]['1_3']['-']/100)
                    + ($data['management'][1]['1_4']['+']/100)*24.5*(1 - $value_1_3_negative), 2);

                // V1 }

            } else {

                // V2 {

                $value_1_3_negative = $value_1_3_negative/120; // 60 за 1.3 плюс 60 за 1.4

                if (1 < $value_1_3_negative) {
                    $value_1_3_negative = 1;
                }

                $part1 = round(($data['management'][1]['1_1']['+']/100)*5.5*(1 - $data['management'][1]['1_1']['-']/100)
                    + ($data['management'][1]['1_2']['+']/100)*15*(1 - $data['management'][1]['1_2']['-']/100)
                    + ($data['management'][1]['1_3']['+']/100)*24.5*(1 - $value_1_3_negative), 2);

                // V2 {
            }

            $part2 = round(($data['management'][2]['2_1']['+']/100)*12*(1 - $data['management'][2]['2_1']['-']/100)
                + ($data['management'][2]['2_2']['+']/100)*6*(1 - $data['management'][2]['2_2']['-']/100)
                + ($data['management'][2]['2_3']['+']/100)*2*(1 - $data['management'][2]['2_3']['-']/100), 2);

            $part3 = round(($data['management'][3]['3_1']['+']/100)*6*(1 - $data['management'][3]['3_1']['-']/100)
                + ($data['management'][3]['3_2']['+']/100)*17*(1 - $data['management'][3]['3_2']['-']/100)
                + ($data['management'][3]['3_3']['+']/100)*5*(1 - $data['management'][3]['3_3']['-']/100)
                + ($data['management'][3]['3_4']['+']/100)*7*(1 - $data['management'][3]['3_4']['-']/100), 2);

            $management_total = $part1 + $part2 + $part3;

            //var_dump($part1, $part2, $part3);

            if ($management_total != $data['management']['total']
                && abs($management_total - $data['management']['total']) > 0.03) {
                echo '| ', $simulation->id, '| 1 | ', $management_total,
                ' | ', $data['management']['total'],
                ' | ', $simulation->end, ' | ', $simulation->user->profile->email, ' |';
                echo "\n";
                $managerial_1_N++;

                $data['management']['total'] = $management_total;
            }

            // save
            if ('true' == $is_fix) {
                $simulation->results_popup_cache = serialize($data);
                $simulation->save(false);
            }
        }

        echo "\n";
        echo $managerial_1_N;
        echo "\n";
    }
}