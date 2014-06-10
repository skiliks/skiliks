<?php
/**
 * Created by PhpStorm.
 * User: slavka
 * Date: 4/30/14
 * Time: 11:15 AM
 */

class FixProductivityCommand extends CConsoleCommand
{
    public function actionIndex($is_fix = 'false') // 7 days
    {
        /** @var Simulation $simulation */
        $simulations = Simulation::model()->findAll(" results_popup_cache is not null
            and scenario_id = 2 and start > '2013-08-01 00:00:00' and status = 'complete' ");

//        $simulations = Simulation::model()->findAll(" id = 5224 ");
//        $simulations = Simulation::model()->findAll(" id = 8802 ");
//        $simulations = Simulation::model()->findAll(" id = 9515 ");
//        $simulations = Simulation::model()->findAll(" id = 6995 ");
//        $simulations = Simulation::model()->findAll(" id = 4995 ");
//        $simulations = Simulation::model()->findAll(" id = 9766 ");
//        $simulations = Simulation::model()->findAll(" id = 5154 ");
//        $simulations = Simulation::model()->findAll(" id = 6491 ");

        $performanceN = 0;

        $max_0 = 80;
        $max_1 = 35;
        $max_2 = 19;
        $max_2_min = 49;

        $wrongSims = [6491,6995,8421,4995,4997,5009,5011,5013,5014,5016,5017,5018,5142,5144,5145,5146,5150,5153,
            5154,5158,5160,5213,5214,5217,5221,5222,5223,5224,5227,5229,5230,5233,5234,5239,5240,5348,5350,5353,
            5355,5358,5359,5364,5402,5406,5411,5413,5414,5416,5417,5418,5420,5488,5508,5672,5687,5721,5831,5898,
            5919,5921,5929,5934,5945,5951,5976,6016,6025,6027,6034,6036,6040,6042,6062,6078,6101,6103,6112,6152,
            6153,6169,6172,6177,6182,6186,6198,6207,6220,6232,6236,6237,6241,6245,6248,6255,6258,6259,6262,6264,
            6267,6272,6277,6281,6283,6285,6290,6295,6309,6310,6343,6368,6372,6381,6385,6398,6412,6414];

        $check = [];
        foreach ($wrongSims as $wrongSim) {
            $check[$wrongSim] = false;
        }

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

            $performancePoints = PerformancePoint::model()->findAllByAttributes(['sim_id' => $simulation->id]);
            /** @var PerformancePoint $performancePoint */
            $sim_perf_data = [
                '0' => 0,
                '1' => 0,
                '2' => 0,
                '2_min' => 0,
            ];
            $userRules = [];
            foreach ($performancePoints as $performancePoint) {

                if (in_array($performancePoint->performanceRule->code, $userRules)) {
                    continue;
                }

                // 41-го нет в новом сценарии
                if (41 != $performancePoint->performanceRule->code) {
                    $userRules[] = $performancePoint->performanceRule->code;
                    $sim_perf_data[$performancePoint->performanceRule->category->code] += $performancePoint->performanceRule->value;
                }

//                echo $performancePoint->id, ' . ', $performancePoint->performanceRule->code, ' : ', $performancePoint->performanceRule->category->code, ' - ' , $performancePoint->performanceRule->value, "\n";
            }

//            $productivityZero = 80 * ((isset($data['performance']['0'])) ? $data['performance']['0'] : 0 );
//            $productivityOne  = 35 * ((isset($data['performance']['1'])) ? $data['performance']['1'] : 0 );
//            $productivityTwo  = 19 * ((isset($data['performance']['2'])) ? $data['performance']['2'] : 0 );
//            $productivity2min = 49 * ((isset($data['performance']['2_min'])) ? $data['performance']['2_min'] : 0 );

            if (false == isset($data['performance']['0'])) {
                $data['performance']['0'] = 0;
            }
            if (false == isset($data['performance']['1'])) {
                $data['performance']['1'] = 0;
            }
            if (false == isset($data['performance']['2'])) {
                $data['performance']['2'] = 0;
            }
            if (false == isset($data['performance']['2_min'])) {
                $data['performance']['2_min'] = 0;
            }

            if (0 != $sim_perf_data['0']
                && $sim_perf_data['0']*100/80 != $data['performance']['0']) {
                $data['performance']['0'] = $sim_perf_data['0']*100/80;
                $check[$simulation->id] = true;
            }
            if (0 != $sim_perf_data['1']
                && $sim_perf_data['1']*100/35 != $data['performance']['1']) {
                $data['performance']['1'] = $sim_perf_data['1']*100/35;
                $check[$simulation->id] = true;
            }
            if (0 != $sim_perf_data['2']
                && $sim_perf_data['2']*100/19 != $data['performance']['2']) {
                $data['performance']['2'] = $sim_perf_data['2']*100/19;
                $check[$simulation->id] = true;
            }
            if (0 != $sim_perf_data['2_min']
                && $sim_perf_data['2_min']*100/49 != $data['performance']['2_min']) {
                $data['performance']['2_min'] = $sim_perf_data['2_min']*100/49;
                $check[$simulation->id] = true;
            }

            $productivityZero = $sim_perf_data['0'];
            $productivityOne  = $sim_perf_data['1'];
            $productivityTwo  = $sim_perf_data['2'];
            $productivity2min = $sim_perf_data['2_min'];

            $performanceTotalValue = round(
                ($productivityZero + $productivityOne + $productivityTwo + $productivity2min)*100 / 183
            , 2);

//            var_dump($sim_perf_data);
//            var_dump($performanceTotalValue);
//            var_dump($data['performance']);
//            die;

            if ($performanceTotalValue != $data['performance']['total']) {
//                echo $simulation->id, ': performance total :' , $performanceTotalValue,
//                ',', $data['performance']['total'],
//                ',', $simulation->end, ',', $simulation->user->profile->email;
//                echo "\n";
                $performanceN++;

                $check[$simulation->id] = true;

                if (false == in_array($simulation->id, $wrongSims)) {
                    //var_dump($sim_perf_data);
                    echo $simulation->id, ': performance total :' , $performanceTotalValue,
                    ',', $data['performance']['total'],
                    ',', $simulation->end, ',', $simulation->user->profile->email;
                    echo "\n";
                }

                $data['performance']['total'] = $performanceTotalValue;
            }

            // save
            if ('true' == $is_fix) {
                $simulation->results_popup_cache = serialize($data);
                $simulation->save(false);
            }
        }

        foreach ($check as $simId => $checkItem) {
            if (false == $checkItem) {
                echo $simId , "\n";
            }
        }

        echo $performanceN;
        echo "\n";
    }
}