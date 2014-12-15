<?php

class CheckAssessmentResults {

    const MODE_DB      = 'db';
    const MODE_CONSOLE = 'console';

    public $mode = self::MODE_DB;

    public $simulationsToIgnore = [15521];

    /**
     * @var SiteLogCheckResults $log
     */
    public $log = null;

    /**
     * @param Simulation array $simulations
     * @param bool $isFix
     */
    public function checkAndFix($simulations, $isFix = false) {
        if (self::MODE_DB == $this->mode) {
            $this->log = new SiteLogCheckResults();
            $this->log->started_by_id = Yii::app()->user->data()->id;
            $this->log->started_at = date('Y-m-d H:i:s');
            $this->log->save();
        }

        $this->checkManagement_1_x($simulations, $isFix);
        $this->checkManagement_2_x($simulations, $isFix);
        $this->checkManagement_3_x($simulations, $isFix);
        $this->checkManagementsTotal($simulations, $isFix);
        $this->checkTime($simulations, $isFix);
        $this->checkProductivity($simulations, $isFix);
        $this->checkOverall($simulations, $isFix);

        if (self::MODE_DB == $this->mode) {
            $this->log->finished_at = date('Y-m-d H:i:s');
            $this->log->save();
        }
    }

    /**
     * @param Simulation array $simulations
     * @param bool $isFix
     */
    public function checkManagement_1_x($simulations, $isFix = false) {
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

            if (in_array($simulation->id, $this->simulationsToIgnore)) {
                continue;
            }

            $data = unserialize($simulation->results_popup_cache);

            if ($data instanceof stdClass) {
                $data = json_decode(json_encode($data), true);
            }

            if (7852 == $simulation->id && false == isset($data['management'][1]['1_5'])) {
                $data['management'][1]['1_5']['-'] = 100;
            }

            if (9515 == $simulation->id && false == isset($data['management'][1]['1_5'])) {
                $data['management'][1]['1_5']['-'] = 100;
            }

            $value_1_1_positive = ($data['management'][1]['1_1']['+'] / 100) * 5.5;
            $value_1_2_positive = ($data['management'][1]['1_2']['+'] / 100) * 15;
            $value_1_3_positive = ($data['management'][1]['1_3']['+'] / 100) * 24.5;

            $value_1_3_negative_db = AssessmentAggregated::model()->findAllByAttributes([
                'sim_id'   => $simulation->id,
                'point_id' => $negative_1_3_behaviours_ids,
            ]);
            $value_1_4_negative_db = AssessmentAggregated::model()->findAllByAttributes([
                'sim_id'   => $simulation->id,
                'point_id' => $negative_1_4_behaviours_ids,
            ]);

            $value_1_3_negative = 0;
            foreach ($value_1_3_negative_db as $value) {
                $value_1_3_negative += abs($value->value);
            }

            $value_1_4_negative = 0;
            foreach ($value_1_4_negative_db as $value) {
                $value_1_4_negative += abs($value->value);
            }

            $value_1_3_total_negative = $value_1_3_negative + $value_1_4_negative;

            if (120 < $value_1_3_total_negative) {
                $value_1_3_total_negative = 120;
            }

            $value_1_1_negative = ($data['management'][1]['1_1']['-'] / 100) * 4;
            $value_1_2_negative = ($data['management'][1]['1_2']['-'] / 100) * 20;


            if ('//simulation_details_popup/v1' == $simulation->results_popup_partials_path) {
                $value_1_1_positive = ($data['management'][1]['1_2']['+'] / 100) * 5.5;
                $value_1_2_positive = ($data['management'][1]['1_3']['+'] / 100) * 15;
                $value_1_3_positive = ($data['management'][1]['1_4']['+'] / 100) * 24.5;

                $value_1_1_negative = ($data['management'][1]['1_2']['-'] / 100) * 4;
                $value_1_2_negative = ($data['management'][1]['1_3']['-'] / 100) * 20;
            }

            $managerial_1_value = (
                    $value_1_1_positive * (1 - $value_1_1_negative/4)
                    + $value_1_2_positive * (1 - $value_1_2_negative/20)
                    + $value_1_3_positive * (1 - $value_1_3_total_negative/120)
                ) / 45;

            $managerial_1_value = round($managerial_1_value * 100, 2);

            // 1.x
            if ($managerial_1_value != $data['management'][1]['total'] &&
                round($managerial_1_value, 0) != round($data['management'][1]['total'], 0)
                && abs(round($managerial_1_value, 2) - round($data['management'][1]['total'], 2)) > 0.5
            ) {
                $this->verboseLineResult(
                    $simulation,
                    '1.х',
                    $data['management'][1]['total'],
                    $managerial_1_value,
                    true
                );
                $managerial_1_N++;

                $data['management'][1]['total'] = $managerial_1_value;
            }

            // v1
            $new_1_3 = number_format($value_1_3_negative*100/60 ,2);
            if (60 < $value_1_3_negative) {
                $new_1_3 = '100.00';
            }
            $new_1_4 = number_format($value_1_4_negative*100/60 ,2);
            if (60 < $value_1_4_negative) {
                $new_1_4 = '100.00';
            }

            if ('//simulation_details_popup/v1' == $simulation->results_popup_partials_path) {

                // [1][3][-]
                if ($new_1_3 != $data['management'][1]['1_4']['-'] &&
                    round($new_1_3, 0) != round($data['management'][1]['1_4']['-'], 0)
                    && abs(round($new_1_3, 0) - round($data['management'][1]['1_4']['-'], 0)) > 0.5
                ) {
                    $this->verboseLineResult(
                        $simulation,
                        '1.3 (v1)',
                        $data['management'][1]['1_4']['-'],
                        $new_1_3,
                        true
                    );

                    $data['management'][1]['1_4']['-'] = $new_1_3;
                }

                if (false == isset($data['management'][1]['1_5'])) {
                    $data['management'][1]['1_5']['-'] = 0;
                }

                // [1][4][-]
                if ($new_1_4 != $data['management'][1]['1_5']['-'] &&
                    round($new_1_4, 0) != round($data['management'][1]['1_5']['-'], 0)
                    && abs(round($new_1_4, 0) - round($data['management'][1]['1_5']['-'], 0)) > 0.5
                ) {
                    $this->verboseLineResult(
                        $simulation,
                        '1.4 (v1)',
                        $data['management'][1]['1_5']['-'],
                        $new_1_4,
                        true
                    );
                    $managerial_1_N++;

                    $data['management'][1]['1_5']['-'] = $new_1_4;
                }
            } else {

                // [1][3][-]
                if ($new_1_3 != $data['management'][1]['1_3']['-'] &&
                    round($new_1_3, 0) != round($data['management'][1]['1_3']['-'], 0)
                    && abs(round($new_1_3, 0) - round($data['management'][1]['1_3']['-'], 0)) > 0.5
                ) {
                    $this->verboseLineResult(
                        $simulation,
                        '1.3 (v2)',
                        $data['management'][1]['1_3']['-'],
                        $new_1_3,
                        true
                    );
                    $managerial_1_N++;

                    $data['management'][1]['1_3']['-'] = $new_1_3;
                }

                // [1][4][-]
                if ($new_1_4 != $data['management'][1]['1_4']['-'] &&
                    round($new_1_4, 0) != round($data['management'][1]['1_4']['-'], 0)
                    && abs(round($new_1_4, 0) - round($data['management'][1]['1_4']['-'], 0)) > 0.5
                ) {
                    $this->verboseLineResult(
                        $simulation,
                        '1.4 (v2)',
                        $data['management'][1]['1_4']['-'],
                        $new_1_4,
                        true
                    );
                    $managerial_1_N++;

                    $data['management'][1]['1_4']['-'] = $new_1_4;
                }
            }

            $this->save($simulation, $data, $isFix);
        }

        $this->verboseGroupResult($simulations, 'Management 1.x', $managerial_1_N);
    }

    /**
     * @param Simulation array $simulations
     * @param bool $isFix
     */
    public function checkManagement_2_x($simulations, $isFix = false) {
        $managerial_2_N = 0;

        foreach ($simulations as $simulation) {

            if (in_array($simulation->id, $this->simulationsToIgnore)) {
                continue;
            }

            if (in_array($simulation->id, $this->simulationsToIgnore)) {
                continue;
            }

            $data = unserialize($simulation->results_popup_cache);

            if ($data instanceof stdClass) {
                $data = json_decode(json_encode($data), true);
            }

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
                && abs(round($managerial_2_value, 0) - round($data['management'][2]['total'], 0)) > 0.5
            ) {
                $this->verboseLineResult(
                    $simulation,
                    '2.х',
                    $data['management'][2]['total'],
                    $managerial_2_value,
                    true
                );

                $managerial_2_N++;

                $data['management'][2]['total'] = $managerial_2_value;
            }

            $this->save($simulation, $data, $isFix);
        }

        $this->verboseGroupResult($simulations, 'Management 2.x', $managerial_2_N);
    }

    /**
     * @param Simulation array $simulations
     * @param bool $isFix
     */
    public function checkManagement_3_x($simulations, $isFix = false) {

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

            if (in_array($simulation->id, $this->simulationsToIgnore)) {
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


            if ('//simulation_details_popup/v1' == $simulation->results_popup_partials_path
                && 5217 != $simulation->id) {
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
                $this->verboseLineResult(
                    $simulation,
                    '3.2.- ' . $type,
                    $data['management'][3]['3_2']['-'],
                    $new_3_2_negative,
                    true
                );
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
                $this->verboseLineResult(
                    $simulation,
                    '3.x ' . $type,
                    $data['management'][3]['total'],
                    $managerial_3_value,
                    true
                );

                $managerial_3_N++;

                $data['management'][3]['total'] = $managerial_3_value;
            }
            // managerial 3 }

            $this->save($simulation, $data, $isFix);
        }

        $this->verboseGroupResult($simulations, 'Management 3.x', $managerial_3_N);
    }

    /**
     * @param Simulation array $simulations
     * @param bool $isFix
     */
    public function checkManagementsTotal($simulations, $isFix = false) {
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

            if (in_array($simulation->id, $this->simulationsToIgnore)) {
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

            if ($management_total != $data['management']['total']
                && abs($management_total - $data['management']['total']) > 0.03) {
                $this->verboseLineResult(
                    $simulation,
                    'management total',
                    $data['management']['total'],
                    $management_total,
                    true
                );

                $managerial_1_N++;

                $data['management']['total'] = $management_total;
            }

            $this->save($simulation, $data, $isFix);
        }

        $this->verboseGroupResult($simulations, 'Management total', $managerial_1_N);
    }

    /**
     * @param Simulation array $simulations
     * @param bool $isFix
     */
    public function checkTime($simulations, $isFix = false) {

        $efficiencyN = 0;
        $firstPriorityN = 0;
        $nonPriorityN = 0;
        $otherPriorityN = 0;

        foreach ($simulations as $simulation) {

            if (in_array($simulation->id, $this->simulationsToIgnore)) {
                continue;
            }

             $data = unserialize($simulation->results_popup_cache);

            if ($data instanceof stdClass) {
                $data = json_decode(json_encode($data), true);
            }

            if (5153 == $simulation->id && 103 < $data['time']['non_priority_mail'] ) {
                $data['time']['non_priority_mail'] = $data['time']['non_priority_mail'] - 103;
            }

            if ((6491 == $simulation->id && 168 == $data['time']['1st_priority_planning'])
                || (6995 == $simulation->id && 172 == $data['time']['1st_priority_mail'])
                || (7046 == $simulation->id && 388 == $data['time']['1st_priority_mail'])
                || (7769 == $simulation->id && 278 == $data['time']['non_priority_mail'])
                || (8360 == $simulation->id && 157 == $data['time']['1st_priority_mail'])
            ) {
                $data['time']['1st_priority_documents'] = $data['time']['1st_priority_documents'] / 2;
                $data['time']['1st_priority_meetings'] = $data['time']['1st_priority_meetings'] / 2;
                $data['time']['1st_priority_phone_calls'] = $data['time']['1st_priority_phone_calls'] / 2;
                $data['time']['1st_priority_mail'] = $data['time']['1st_priority_mail'] / 2;
                $data['time']['1st_priority_planning'] = $data['time']['1st_priority_planning'] / 2;

                $data['time']['non_priority_documents'] = $data['time']['non_priority_documents'] / 2;
                $data['time']['non_priority_meetings'] = $data['time']['non_priority_meetings'] / 2;
                $data['time']['non_priority_phone_calls'] = $data['time']['non_priority_phone_calls'] / 2;
                $data['time']['non_priority_mail'] = $data['time']['non_priority_mail'] / 2;
                $data['time']['non_priority_planning'] = $data['time']['non_priority_planning'] / 2;
            }

            if (8421 == $simulation->id && 349 < $data['time']['1st_priority_planning']) {
                $data['time']['1st_priority_documents'] = $data['time']['1st_priority_documents'] / 3;
                $data['time']['1st_priority_meetings'] = $data['time']['1st_priority_meetings'] / 3;
                $data['time']['1st_priority_phone_calls'] = $data['time']['1st_priority_phone_calls'] / 3;
                $data['time']['1st_priority_mail'] = $data['time']['1st_priority_mail'] / 3;
                $data['time']['1st_priority_planning'] = $data['time']['1st_priority_planning'] / 3;

                $data['time']['non_priority_documents'] = $data['time']['non_priority_documents'] / 3;
                $data['time']['non_priority_meetings'] = $data['time']['non_priority_meetings'] / 3;
                $data['time']['non_priority_phone_calls'] = $data['time']['non_priority_phone_calls'] / 3;
                $data['time']['non_priority_mail'] = $data['time']['non_priority_mail'] / 3;
                $data['time']['non_priority_planning'] = $data['time']['non_priority_planning'] / 3;
            }

            if ((6062 == $simulation->id && 244 == $data['time']['1st_priority_mail']) ||
                (7383 == $simulation->id && 240 < $data['time']['1st_priority_mail']) ||
                9148 == $simulation->id && 150 < $data['time']['1st_priority_mail']) {
                $data['time']['1st_priority_documents'] = $data['time']['1st_priority_documents'] / 1.3;
                $data['time']['1st_priority_meetings'] = $data['time']['1st_priority_meetings'] / 1.3;
                $data['time']['1st_priority_phone_calls'] = $data['time']['1st_priority_phone_calls'] / 1.3;
                $data['time']['1st_priority_mail'] = $data['time']['1st_priority_mail'] / 1.3;
                $data['time']['1st_priority_planning'] = $data['time']['1st_priority_planning'] / 1.3;

                $data['time']['non_priority_documents'] = $data['time']['non_priority_documents'] / 1.3;
                $data['time']['non_priority_meetings'] = $data['time']['non_priority_meetings'] / 1.3;
                $data['time']['non_priority_phone_calls'] = $data['time']['non_priority_phone_calls'] / 1.3;
                $data['time']['non_priority_mail'] = $data['time']['non_priority_mail'] / 1.3;
                $data['time']['non_priority_planning'] = $data['time']['non_priority_planning'] / 1.3;
            }

            if (14121 == $simulation->id && 126 != $data['time']['1st_priority_meetings']) {
                $data['time']['1st_priority_meetings'] = 126;

                $log = UniversalLog::model()->findByAttributes(['sim_id' => $simulation->id, 'end_time' => '00:00:00']);
                if (null !== $log) {
                    if ($log->start_time < '18:01:00') {
                        $log->end_time = '18:00:00';
                        die(1);
                    } else {
                        $log->end_time = $log->start_time;
                    }

                    $log->save();
                }
            }

//             time_spend {
            $fullDuration = 8*60 + 15 + $data['time']['workday_overhead_duration'];
            $firstPriorityDuration = $data['time']['1st_priority_documents']
                + $data['time']['1st_priority_meetings']
                + $data['time']['1st_priority_phone_calls']
                + $data['time']['1st_priority_mail']
                + $data['time']['1st_priority_planning'];
            $nonPriorityDuration = $data['time']['non_priority_documents']
                + $data['time']['non_priority_meetings']
                + $data['time']['non_priority_phone_calls']
                + $data['time']['non_priority_mail']
                + $data['time']['non_priority_planning'];

            // 120 < overtime
            if (120 < $data['time']['workday_overhead_duration']) {
                $this->verboseLineResult(
                    $simulation,
                    'overtime',
                    $data['time']['workday_overhead_duration'],
                    120,
                    true
                );

                $data['time']['workday_overhead_duration'] = 120;
                $fullDuration = 8*60 + 15 + $data['time']['workday_overhead_duration'];
            }

            // $fullDuration < $firstPriorityDuration + $nonPriorityDuration
            if ($fullDuration < $firstPriorityDuration + $nonPriorityDuration) {
                $this->verboseLineResult(
                    $simulation,
                    'duration',
                    $fullDuration,
                    $firstPriorityDuration + $nonPriorityDuration,
                    true
                );

                continue;
            }

            $firstPriorityValue = round(100*$firstPriorityDuration/$fullDuration, 2);
            $nonPriorityValue = round(100*$nonPriorityDuration/$fullDuration, 2);
            $otherValue = round(100 - $firstPriorityValue - $nonPriorityValue, 2);

            if (-0 == $otherValue) {
                $otherValue = 0;
            }

            if ($firstPriorityValue != $data['time']['time_spend_for_1st_priority_activities']
                && round($firstPriorityValue) != $data['time']['time_spend_for_1st_priority_activities']
                && round($firstPriorityValue) + 1 != $data['time']['time_spend_for_1st_priority_activities']
                && round($firstPriorityValue) - 1 != $data['time']['time_spend_for_1st_priority_activities']) {
                $this->verboseLineResult(
                    $simulation,
                    'time_spend_for_1st_priority_activities',
                    $data['time']['time_spend_for_1st_priority_activities'],
                    $firstPriorityValue,
                    true
                );

                $firstPriorityN++;
                $data['time']['time_spend_for_1st_priority_activities'] = $firstPriorityValue;
            }

            if ($nonPriorityValue != $data['time']['time_spend_for_non_priority_activities']
                && round($nonPriorityValue) != $data['time']['time_spend_for_non_priority_activities']
                && round($nonPriorityValue) + 1 != $data['time']['time_spend_for_non_priority_activities']
                && round($nonPriorityValue) - 1 != $data['time']['time_spend_for_non_priority_activities']) {
                $this->verboseLineResult(
                    $simulation,
                    'time_spend_for_non_priority_activities',
                    $data['time']['time_spend_for_non_priority_activities'],
                    $nonPriorityValue,
                    true
                );

                $nonPriorityN++;
                $data['time']['time_spend_for_non_priority_activities'] = $nonPriorityValue;
            }

            if ($otherValue != $data['time']['time_spend_for_inactivity']
                && round($otherValue) != $data['time']['time_spend_for_inactivity']) {
                $this->verboseLineResult(
                    $simulation,
                    'time_spend_for_inactivity',
                    $data['time']['time_spend_for_inactivity'],
                    $otherValue,
                    true
                );

                $otherPriorityN++;
                $data['time']['time_spend_for_inactivity'] = $otherValue;
            }
//             time_spend }

//             efficiency {
            $efficiency = $data['time']['time_spend_for_1st_priority_activities']*2/3
                + ((1 - $data['time']['workday_overhead_duration']/120)*100)/3;

            $efficiency = round($efficiency, 2);

            if ( $efficiency != $data['time']['total'] || $efficiency != $data['time']['efficiency']) {
                $this->verboseLineResult(
                    $simulation,
                    'efficiency',
                    $data['time']['total'],
                    $efficiency,
                    true
                );

                $efficiencyN++;

                $data['time']['total'] = $efficiency;
                $data['time']['efficiency'] = $efficiency;
            }
            // efficiency }

            $this->save($simulation, $data, $isFix);
        }

        $this->verboseGroupResult($simulations, 'Time management, 1st priority', $firstPriorityN);
        $this->verboseGroupResult($simulations, 'Time management, non priority', $nonPriorityN);
        $this->verboseGroupResult($simulations, 'Time management, other priority', $otherPriorityN);
        $this->verboseGroupResult($simulations, 'Time management', $efficiencyN);
    }

    /**
     * @param Simulation array $simulations
     * @param bool $isFix
     */
    public function checkProductivity($simulations, $isFix = false) {
        $performanceN = 0;

        $max_0 = 80;
        $max_1 = 35;
        $max_2 = 19;
        $max_2_min = 49;

        foreach ($simulations as $simulation) {

            if (in_array($simulation->id, $this->simulationsToIgnore)) {
                continue;
            }

            $data = unserialize($simulation->results_popup_cache);

            if ($data instanceof stdClass) {
                $data = json_decode(json_encode($data), true);
            }

            // считаем баллы по данным БД {
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
            }
            // считаем баллы по данным БД }

            // в прошлом формате кеша если оценка "0" - то элеиент массива отсутствовал {
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
            // в прошлом формате кеша если оценка "0" - то элеиент массива отсутствовал }

            if (0 != $sim_perf_data['0']
                && $sim_perf_data['0']*100/80 != $data['performance']['0']) {
                $data['performance']['0'] = $sim_perf_data['0']*100/80;
            }
            if (0 != $sim_perf_data['1']
                && $sim_perf_data['1']*100/35 != $data['performance']['1']) {
                $data['performance']['1'] = $sim_perf_data['1']*100/35;
            }
            if (0 != $sim_perf_data['2']
                && $sim_perf_data['2']*100/19 != $data['performance']['2']) {
                $data['performance']['2'] = $sim_perf_data['2']*100/19;
            }
            if (0 != $sim_perf_data['2_min']
                && $sim_perf_data['2_min']*100/49 != $data['performance']['2_min']) {
                $data['performance']['2_min'] = $sim_perf_data['2_min']*100/49;
            }

            $productivityZero = $sim_perf_data['0'];
            $productivityOne  = $sim_perf_data['1'];
            $productivityTwo  = $sim_perf_data['2'];
            $productivity2min = $sim_perf_data['2_min'];

            $performanceTotalValue = round(
                ($productivityZero + $productivityOne + $productivityTwo + $productivity2min)*100 / 183
                , 2);


            if ($performanceTotalValue != $data['performance']['total']) {
                $performanceN++;

                echo $simulation->id, ': performance total :' , $performanceTotalValue,
                ',', $data['performance']['total'],
                ',', $simulation->end, ',', $simulation->user->profile->email;
                echo "\n";

                $this->verboseLineResult(
                    $simulation,
                    'Performance',
                    $data['performance']['total'],
                    $performanceTotalValue,
                    true
                );

                $data['performance']['total'] = $performanceTotalValue;
            }

            $this->save($simulation, $data, $isFix);
        }

        $this->verboseGroupResult($simulations, 'Performance', $performanceN);
    }

    /**
     * @param Simulation array $simulations
     * @param bool $isFix
     */
    public function checkOverall($simulations, $isFix = false) {
        $overallN = 0;

        foreach ($simulations as $simulation) {

            if (in_array($simulation->id, $this->simulationsToIgnore)) {
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
                $this->verboseLineResult(
                    $simulation,
                    'Overall',
                    $data['overall'],
                    $newOverall,
                    true
                );
                $overallN++;

                $data['overall'] = $newOverall;
            }

            $this->save($simulation, $data, $isFix);
        }

        $this->verboseGroupResult($simulations, 'Overall', $overallN);
    }

    /**
     * @param Simulation $simulation
     * @param array $data
     * @param bool $isFix
     */
    public function save($simulation, $data, $isFix = false) {
        if (true == $isFix && false == in_array($simulation->id, $this->simulationsToIgnore)) {
            $simulation->results_popup_cache = serialize($data);
            $simulation->save(false);
        }
    }

    /**
     * @param $simulation
     * @param $valueLabel
     * @param $currentValue
     * @param $rightValue
     * @param $isWrong
     */
    public function verboseLineResult($simulation, $valueLabel, $currentValue, $rightValue, $isWrong) {
        if (false == $isWrong) {
            return null;
        }

        if (self::MODE_DB == $this->mode) {
            // Текст для хранения в бд
            $this->log->result .= 'Error in simulation ' . $simulation->id . ' in parameter ' . $valueLabel
                . ' current value ' . $currentValue . ', but it must be ' . $rightValue . '. User email ' . $simulation->user->profile->email
                . ', simulation finished at ' . $simulation->end . '<br/>';
            $this->log->save();
        } elseif (self::MODE_CONSOLE == $this->mode) {
            // Текст для вывода в консоль
            echo '| ', $simulation->id, ' | ', $valueLabel, ' | ', $currentValue,
                ' | ', $rightValue, ' | ', $simulation->end, ' | ',
                $simulation->user->profile->email, ' |';
            echo "\n";
        }
    }

    /**
     * @param $simulation
     * @param $valueLabel
     * @param $currentValue
     * @param $rightValue
     * @param $isWrong
     */
    public function verboseGroupResult($simulations, $valueLabel, $errorsCounter) {
        if (0 == $errorsCounter) {
            if (self::MODE_DB == $this->mode) {
                // Текст для хранения в бд
                $this->log->result .= count($simulations) . ' sim`s checked: all right with ' . $valueLabel
                . '<br/>==========================================<br/>';
                $this->log->save();
            } elseif (self::MODE_CONSOLE == $this->mode) {
                // Текст для вывода в консоль
                echo "\n";
                echo $errorsCounter;
                echo "\n";
            }
        } else {
            if (self::MODE_DB == $this->mode) {
                // Текст для хранения в бд
                $this->log->result .= count($simulations) . ' sim`s checked: ' . $errorsCounter . ' errors were found in '
                    . $valueLabel . '<br/>';
                $this->log->save();
            } elseif (self::MODE_CONSOLE == $this->mode) {
                // Текст для вывода в консоль
                echo "\n";
                echo $errorsCounter;
                echo "\n";
            }
        }
    }
} 