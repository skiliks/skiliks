<?php

class PDFController extends SiteBaseController {

    public function actionSimulationDetailPDF()
    {

        $this->user = Yii::app()->user->data();
        if(null === $this->user && false === $this->user->isAuth()) {
            $this->redirect('/registration');
        }

        $sim_id = $this->getParam('sim_id');
        /* @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($sim_id);
        $isUser = $simulation->user_id === $this->user->id;
        //var_dump('$isUser', $isUser);
        $isOwner = $simulation->invite->owner_id === $this->user->id;
        //var_dump('$isOwner', $isOwner);
        $isAdmin = $this->user->isAdmin();
        //var_dump('$isAdmin', $isAdmin);
        //exit;
        if($isUser || $isOwner || $isAdmin) {
            $data = json_decode($simulation->getAssessmentDetails(), true);

            $pdf = new AssessmentPDF();
            $username = $this->user->profile->firstname.' '.$this->user->profile->lastname;
            $pdf->addPage();
            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addRatingPercentile(92.4, 37.6, $data['percentile']['total']);
            $pdf->addRatingOverall(85, 48, $data['overall']);
            $pdf->addSpeedometer(19.8, 109.2, $data['time']['total']);
            $pdf->addSpeedometer(87.9, 109.2, $data['performance']['total']);
            $pdf->addSpeedometer(156.9, 109.2, $data['management']['total']);

            $pdf->addPage();
            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addPercentSmallInfo($data['time']['total'], 184.1, 28.4);
            //$pdf->writeTextBold(round($data['time']['total']).'%', 184.1, 28.4, 10, array(255,255,255));
            $pdf->addTimeDistribution(53.9, 89.7,
                $data['time']['time_spend_for_1st_priority_activities'],
                $data['time']['time_spend_for_non_priority_activities'],
                $data['time']['time_spend_for_inactivity']
            );
            $pdf->addOvertime(156.2, 90.7, $data['time']['workday_overhead_duration']);


            $pdf->addPercentSmallInfo($data['time']['total'], 177, 175.84);
            //$pdf->writeTextBold(round($data['time']['total']).'%', 177, 175.84, 10, [255,255,255]);//Распеределение времени


            //$pdf->addPercentSmallInfo($data['time'][TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES], 184.1, 28.4);
            $pdf->addPercentMiddleInfo($data['time'][TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES],
                82.1, 197.5);//Продуктивное время


            $pdf->addPercentMiddleInfo($data['time'][TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES], 185, 197.5);//Не продуктивное время
            //Positive
            $x_positive = 33;
            $max_positive = $pdf->getMaxTimePositive($data['time']);
            $pdf->addTimeBarProductive($x_positive, 218, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS], $max_positive); //Документы

            $pdf->addTimeBarProductive($x_positive, 228.5, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS], $max_positive);//Встречи

            $pdf->addTimeBarProductive($x_positive, 239, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS], $max_positive);//Звонки

            $pdf->addTimeBarProductive($x_positive, 249.5, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL], $max_positive);//Почта

            $pdf->addTimeBarProductive($x_positive, 260, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING], $max_positive);//План

            //Negative
            $y_positive = 137;
            $max_negative = $pdf->getMaxTimeNegative($data['time']);
            $pdf->addTimeBarUnproductive($y_positive, 218, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS], $max_negative); //Документы

            $pdf->addTimeBarUnproductive($y_positive, 228.5, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS], $max_negative);//Встречи

            $pdf->addTimeBarUnproductive($y_positive, 239, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS], $max_negative);//Звонки

            $pdf->addTimeBarUnproductive($y_positive, 249.5, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL], $max_negative);//Почта

            $pdf->addTimeBarUnproductive($y_positive, 260, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING], $max_negative);//План

            $pdf->addPage();
            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            //$pdf->writeTextBold(round($data['performance']['total']).'%', 134, 27.8, 10, [255,255,255]);//Результативность
            $pdf->addPercentSmallInfo($data['performance']['total'], 134, 27.8);
            $pdf->addUniversalBar(77, 45.8, $pdf->getPerformanceCategory($data['performance'], '0'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//Срочно
            $pdf->addUniversalBar(77, 56.3, $pdf->getPerformanceCategory($data['performance'], '1'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//Высокий приоритет
            $pdf->addUniversalBar(77, 66.9, $pdf->getPerformanceCategory($data['performance'], '2'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//Средний приоритет
            $pdf->addUniversalBar(77, 77.5, $pdf->getPerformanceCategory($data['performance'], '2_min'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//Двухминутные задачи

            $pdf->addPage();

            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            //$pdf->writeTextBold(round($data['management']['total']).'%', 149.3, 28.3, 10, [255,255,255]);//Управленческие навыки
            $pdf->addPercentSmallInfo($data['management']['total'], 149.3, 28.3);

            $pdf->addUniversalBar(77.7, 48.9, $data['management'][1]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//1
            $pdf->addUniversalBar(77.7, 59.5, $data['management'][2]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//2
            $pdf->addUniversalBar(77.7, 70.1, $data['management'][3]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//3


            $pdf->addPage();
            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            //$pdf->writeTextBold('100%', 149.9, 28.2, 10, [255,255,255]);//1
            //$pdf->writeTextBold(round($data['management'][1]['total']).'%', 3.4, 36.8, 18);
            $pdf->addPercentBigInfo(/*$data['management'][1]['total']*/39, 3.4, 36.8);

            $pdf->addUniversalBar(77, 60, $data['management'][1]['1_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.1 positive
            $pdf->addUniversalBar(77, 70.6, $data['management'][1]['1_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.2 positive
            $pdf->addUniversalBar(77, 81.2, $data['management'][1]['1_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.3 positive

            $pdf->addUniversalBar(152, 60, $data['management'][1]['1_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.1 negative
            $pdf->addUniversalBar(152, 70.6, $data['management'][1]['1_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.2 negative
            $pdf->addUniversalBar(152, 81.2, $data['management'][1]['1_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.3 negative
            $pdf->addUniversalBar(152, 91.8, $data['management'][1]['1_4']['-'], 54.14, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_NEGATIVE);//1.4 negative

            $pdf->addPage();
            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            //$pdf->writeTextBold('100%', 149.9, 27.8, 10, [255,255,255]);//1
            //$pdf->writeTextBold(round($data['management'][2]['total']).'%', 2.8, 36.8, 18);
            $pdf->addPercentBigInfo($data['management'][2]['total'], 2.8, 36.8);

            $pdf->addUniversalBar(77, 60, $data['management'][2]['2_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.1 positive
            $pdf->addUniversalBar(77, 70.6, $data['management'][2]['2_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.2 positive
            $pdf->addUniversalBar(77, 81.2, $data['management'][2]['2_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.3 positive

            $pdf->addUniversalBar(152, 60, $data['management'][2]['2_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.1 negative
            $pdf->addUniversalBar(152, 70.6, $data['management'][2]['2_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.2 negative
            $pdf->addUniversalBar(152, 81.2, $data['management'][2]['2_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.3 negative

            $pdf->addPage();

            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            //$pdf->writeTextBold('100%', 148.7, 28, 10, [255,255,255]);//1
            //$pdf->writeTextBold(round($data['management'][3]['total']).'%', 2.8, 36.8, 18);
            $pdf->addPercentBigInfo($data['management'][3]['total'], 2.8, 36.8);

            $pdf->addUniversalBar(77, 60, $data['management'][3]['3_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.1 positive
            $pdf->addUniversalBar(77, 70.6, $data['management'][3]['3_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.2 positive
            $pdf->addUniversalBar(77, 81.2, $data['management'][3]['3_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.3 positive
            $pdf->addUniversalBar(77, 91.8, $data['management'][3]['3_4']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.4 positive

            $pdf->addUniversalBar(152, 60, $data['management'][3]['3_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.1 negative
            $pdf->addUniversalBar(152, 70.6, $data['management'][3]['3_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.2 negative
            $pdf->addUniversalBar(152, 81.2, $data['management'][3]['3_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.3 negative
            $pdf->addUniversalBar(152, 91.8, $data['management'][3]['3_4']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.4 negative

            $pdf->renderOnBrowser('Assessment_v2');
        } else {
            $this->redirect('/dashboard');
        }


    }

} 