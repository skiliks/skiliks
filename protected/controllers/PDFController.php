<?php

class PDFController extends SiteBaseController {

    public function actionSimulationDetailPDF()
    {

        $this->user = Yii::app()->user->data();
        if(null === $this->user && false === $this->user->isAuth()) {
            $this->redirect('/registration');
        }

        $simId = 11056; //$this->getParam('sim_id');
        $assessmentVersion = 'v2';//$this->getParam('assessment_version');


        /* @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);
        $isUser = $simulation->user_id === $this->user->id;
        $isOwner = $simulation->invite->owner_id === $this->user->id;
        $isAdmin = $this->user->isAdmin();

        if($isUser || $isOwner || $isAdmin) {
            $data = json_decode($simulation->getAssessmentDetails(), true);

            //$popup_tests_cache = SimulationResultTextService::generate($simulation, 'popup');
            //var_dump($popup_tests_cache);
            //exit;
            $pdf = new AssessmentPDF();

            $username = $simulation->user->profile->firstname.' '.$simulation->user->profile->lastname;

            $pdf->setImagesDir('simulation_details_'.$assessmentVersion.'/images/');

        // 1. Спидометры и прочее
            /*$pdf->addPage();
            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addRatingPercentile(94, 35.6, $data['percentile']['total']);
            $pdf->addRatingOverall(86.6, 45.8, $data['overall']);
            $pdf->addSpeedometer(21, 107.2, $data['time']['total']);
            $pdf->addSpeedometer(89, 107.2, $data['performance']['total']);
            $pdf->addSpeedometer(158, 107.2, $data['management']['total']);*/

        // 2. Тайм менеджмент
            $pdf->addPage(2);
            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addPercentSmallInfo($data['time']['total'], 183.1, 27.8);
            $pdf->writeTextRegular('(очень высокий уровень)', 70, 33, 16);
            $pdf->addTimeDistribution(
                55.7,
                89.4,
                $data['time']['time_spend_for_1st_priority_activities'],
                $data['time']['time_spend_for_non_priority_activities'],
                $data['time']['time_spend_for_inactivity']
            );
            $pdf->addOvertime(158.1, 90.2, $data['time']['workday_overhead_duration']);


            $pdf->addPercentSmallInfo($data['time']['total'], 179, 175.84);

            /*$pdf->addPercentMiddleInfo(
                $data['time'][TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES],
                82.1,
                197.5
            ); //Продуктивное время

            $pdf->addPercentMiddleInfo($data['time'][TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES], 185, 197.5);//Не продуктивное время

            //Positive
            $x_positive = 33;
            $max_positive = $pdf->getMaxTimePositive($data['time']);

            //Документы
            $pdf->addTimeBarProductive($x_positive, 218, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS], $max_positive);

            //Встречи
            $pdf->addTimeBarProductive($x_positive, 228.5, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS], $max_positive);

            //Звонки
            $pdf->addTimeBarProductive($x_positive, 239, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS], $max_positive);

            //Почта
            $pdf->addTimeBarProductive($x_positive, 249.5, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL], $max_positive);

            //План
            $pdf->addTimeBarProductive($x_positive, 260, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING], $max_positive);

            //Negative
            $y_positive = 137;
            $max_negative = $pdf->getMaxTimeNegative($data['time']);

            //Документы
            $pdf->addTimeBarUnproductive($y_positive, 218, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS], $max_negative);

            //Встречи
            $pdf->addTimeBarUnproductive($y_positive, 228.5, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS], $max_negative);

            //Звонки
            $pdf->addTimeBarUnproductive($y_positive, 239, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS], $max_negative);

            //Почта
            $pdf->addTimeBarUnproductive($y_positive, 249.5, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL], $max_negative);

            //План
            $pdf->addTimeBarUnproductive($y_positive, 260, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING], $max_negative);

        // 3. Результативность
            $pdf->addPage();
            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addPercentSmallInfo($data['performance']['total'], 134, 27.8);

            //Срочно
            $pdf->addUniversalBar(77, 45.8, $pdf->getPerformanceCategory($data['performance'], '0'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

            //Высокий приоритет
            $pdf->addUniversalBar(77, 56.3, $pdf->getPerformanceCategory($data['performance'], '1'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

            //Средний приоритет
            $pdf->addUniversalBar(77, 66.9, $pdf->getPerformanceCategory($data['performance'], '2'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

            //Двухминутные задачи
            $pdf->addUniversalBar(77, 77.5, $pdf->getPerformanceCategory($data['performance'], '2_min'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        // 4. Управленческие навыки
            $pdf->addPage();

            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addPercentSmallInfo($data['management']['total'], 149.3, 28.3);

            $pdf->addUniversalBar(77.7, 48.9, $data['management'][1]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//1
            $pdf->addUniversalBar(77.7, 59.5, $data['management'][2]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//2
            $pdf->addUniversalBar(77.7, 70.1, $data['management'][3]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//3

            if (Simulation::ASSESSMENT_VERSION_1 == $assessmentVersion) {
                // 5. Управленческие навыки - 1 по версии v1
                $pdf->page_number = 5;
                $pdf->addPage();
                $pdf->writeTextBold($username, 3.5, 3.5, 21);
                $pdf->addPercentBigInfo($data['management'][1]['total'], 3.4, 36.8);

                $pdf->addUniversalBar(77, 58.7,   $data['management'][1]['1_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.1 positive
                $pdf->addUniversalBar(77, 69.0, $data['management'][1]['1_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.2 positive
                $pdf->addUniversalBar(77, 79.0, $data['management'][1]['1_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.3 positive
                $pdf->addUniversalBar(77, 89.0, $data['management'][1]['1_4']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.4 positive

                $pdf->addUniversalBar(152, 58.7,    $data['management'][1]['1_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.1 negative
                $pdf->addUniversalBar(152, 69.0,  $data['management'][1]['1_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.2 negative
                $pdf->addUniversalBar(152, 79.0,  $data['management'][1]['1_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.3 negative
                $pdf->addUniversalBar(152, 89.0,  $data['management'][1]['1_4']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT,  AssessmentPDF::BAR_NEGATIVE);//1.4 negative
                $pdf->addUniversalBar(152, 99.0, $data['management'][1]['1_5']['-'], 54.14, AssessmentPDF::ROUNDED_BOTH,  AssessmentPDF::BAR_NEGATIVE);//1.5 negative
            }

            if (Simulation::ASSESSMENT_VERSION_2 == $assessmentVersion) {
                // 5. Управленческие навыки - 1 по версии v2
                $pdf->addPage();
                $pdf->writeTextBold($username, 3.5, 3.5, 21);
                $pdf->addPercentBigInfo($data['management'][1]['total'], 3.4, 36.8);

                $pdf->addUniversalBar(77, 60, $data['management'][1]['1_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.1 positive
                $pdf->addUniversalBar(77, 70.6, $data['management'][1]['1_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.2 positive
                $pdf->addUniversalBar(77, 81.2, $data['management'][1]['1_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.3 positive

                $pdf->addUniversalBar(152, 60, $data['management'][1]['1_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.1 negative
                $pdf->addUniversalBar(152, 70.6, $data['management'][1]['1_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.2 negative
                $pdf->addUniversalBar(152, 81.2, $data['management'][1]['1_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.3 negative
                $pdf->addUniversalBar(152, 91.8, $data['management'][1]['1_4']['-'], 54.14, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_NEGATIVE);//1.4 negative
            }

        // 6. Управленческие навыки - 2
            $pdf->addPage();
            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addPercentBigInfo($data['management'][2]['total'], 2.8, 36.8);

            $pdf->addUniversalBar(77, 60, $data['management'][2]['2_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.1 positive
            $pdf->addUniversalBar(77, 70.6, $data['management'][2]['2_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.2 positive
            $pdf->addUniversalBar(77, 81.2, $data['management'][2]['2_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.3 positive

            $pdf->addUniversalBar(152, 60, $data['management'][2]['2_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.1 negative
            $pdf->addUniversalBar(152, 70.6, $data['management'][2]['2_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.2 negative
            $pdf->addUniversalBar(152, 81.2, $data['management'][2]['2_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.3 negative

        // 7. Управленческие навыки - 3
            $pdf->addPage();

            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addPercentBigInfo($data['management'][3]['total'], 2.8, 36.8);

            $pdf->addUniversalBar(77, 60, $data['management'][3]['3_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.1 positive
            $pdf->addUniversalBar(77, 70.6, $data['management'][3]['3_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.2 positive
            $pdf->addUniversalBar(77, 81.2, $data['management'][3]['3_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.3 positive
            $pdf->addUniversalBar(77, 91.8, $data['management'][3]['3_4']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.4 positive

            $pdf->addUniversalBar(152, 60, $data['management'][3]['3_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.1 negative
            $pdf->addUniversalBar(152, 70.6, $data['management'][3]['3_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.2 negative
            $pdf->addUniversalBar(152, 81.2, $data['management'][3]['3_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.3 negative
            $pdf->addUniversalBar(152, 91.8, $data['management'][3]['3_4']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.4 negative
            */
            $first_name = StringTools::CyToEnWithUppercase($simulation->user->profile->firstname);
            $last_name = StringTools::CyToEnWithUppercase($simulation->user->profile->lastname);
            $vacancy_name = "";
            if($simulation->invite->owner_id !== $simulation->invite->receiver_id) {
                $vacancy_name = "_".StringTools::CyToEnWithUppercase($simulation->invite->vacancy->label);
            }
            $pdf->renderOnBrowser($first_name.'_'.$last_name.$vacancy_name.'_'.$assessmentVersion.'_'.date('dmy'));
        } else {
            $this->redirect('/dashboard');
        }
    }

    public function actionSimulationDetailPDFBank(){

        $simulation = Simulation::model()->findByPk(10264);
        SimulationService::saveAssessmentPDFFilesOnDisk($simulation);

    }

} 