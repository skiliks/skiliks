<?php

class PDFController extends SiteBaseController {

    public function actionSimulationDetailPDF()
    {

        $this->user = Yii::app()->user->data();
        if(null === $this->user && false === $this->user->isAuth()) {
            $this->redirect('/registration');
        }

        //$simId = 5013; //$this->getParam('sim_id');
        $simId = $this->getParam('sim_id');

        /* @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);
        $simulation->popup_tests_cache = serialize([
            'popup' => SimulationResultTextService::generate($simulation, 'popup'),
            'recommendation' => SimulationResultTextService::generate($simulation, 'recommendation', true)
        ]);
        $simulation->save(false);
        $isUser = $simulation->user_id === $this->user->id;
        $isOwner = $simulation->invite->owner_id === $this->user->id;
        $isAdmin = $this->user->isAdmin();

        if($isUser || $isOwner || $isAdmin) {
            $first_name = StringTools::CyToEnWithUppercase($simulation->user->profile->firstname);
            $last_name = StringTools::CyToEnWithUppercase($simulation->user->profile->lastname);
            $path = __DIR__.'/../system_data/simulation_details/';

            $filename = $first_name.'_'.$last_name.'_'.date('dmy', strtotime($simulation->end)).'.zip';
            if(false === file_exists($path.'/'.$filename)){

                $zip = new ZipArchive();
                $zip->open($path.'/'.$filename, ZIPARCHIVE::CREATE);

                $zip->addFile($path.'/'.$this->createSimulationDetailPDF($simulation, $path).'.pdf', '/'.$this->createSimulationDetailPDF($simulation, $path).'.pdf');
                $zip->addFile($path.'/'.$this->createBehavioursPDF($simulation, $path).'.pdf', '/'.$this->createBehavioursPDF($simulation, $path).'.pdf');
                $zip->close();
            }


            header('Content-Type: application/zip; charset=utf-8');
            header('Content-Disposition: attachment; filename="'.$filename.'"');

            $File = file_get_contents($path.'/'.$filename);

            echo $File;
        } else {
            $this->redirect('/dashboard');
        }
    }


    private function createSimulationDetailPDF(Simulation $simulation, $path, $save = true) {
        $assessmentVersion = $simulation->assessment_version;
        $data = json_decode($simulation->getAssessmentDetails(), true);

        if (null == $simulation->popup_tests_cache) {
            //$popup_tests_cache = SimulationResultTextService::generate($simulation, 'popup');
            $simulation->popup_tests_cache = serialize([
                'popup' => SimulationResultTextService::generate($simulation, 'popup')
            ]);
            $simulation->save(false);
        }
        $popup_tests_cache = unserialize($simulation->popup_tests_cache)['popup'];
        //echo $simulation->getAssessmentDetails();
        //exit;
        $pdf = new AssessmentPDF();
        $username = $simulation->user->profile->firstname.' '.$simulation->user->profile->lastname;

        $pdf->setImagesDir('simulation_details_'.$assessmentVersion.'/images/');

        $pdf->pdf->setCellHeightRatio(1);

        // 1. Спидометры и прочее
        $pdf->addPage(1);

        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->addRatingPercentile(94, 38.0, $data['percentile']['total']);
        $pdf->addRatingOverall(86.6, 48.8, $data['overall']);
        $pdf->addSpeedometer(21, 109.7, $data['time']['total']);
        $pdf->addSpeedometer(89, 109.7, $data['performance']['total']);
        $pdf->addSpeedometer(158, 109.7, $data['management']['total']);

        // 2. Тайм менеджмент
        //======================================================================================
        $pdf->addPage(2);


        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->addPercentSmallInfo($data['time']['total'], 183.1, 27.8);
        $pdf->writeTextCenterRegular(90, 10, 65, 33, 16, $popup_tests_cache['time']['short_text']);//(очень высокий уровень)

        $pdf->addTimeDistribution(
            55.7,
            89.4,
            $data['time']['time_spend_for_1st_priority_activities'],
            $data['time']['time_spend_for_non_priority_activities'],
            $data['time']['time_spend_for_inactivity']
        );
        $pdf->addOvertime(158.1, 90.2, $data['time']['workday_overhead_duration']);
        $pdf->writeHtml('
                    <tr>
                        <td style="width: 5.5%;"></td>
                        <td  style="width: 42%; padding: 0px;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">Продуктивное время</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt; ">'.$popup_tests_cache['time.productive_time']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['time.productive_time']['text'].'</font><br>
                        </td>
                        <td style="width: 6%;"></td>
                        <td style="width: 42%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">Сверхурочное время</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['time.over_time']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['time.over_time']['text'].'</font><br>
                        </td>
                        <td style="width: 4.5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 5.5%;"></td>
                        <td  style="width: 42%; padding: 0px;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">Непродуктивное время</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt; ">'.$popup_tests_cache['time.not_productive_time']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['time.not_productive_time']['text'].'</font><br>
                        </td>
                        <td style="width: 6%;"></td>
                        <td style="width: 42%;"></td>
                        <td style="width: 4.5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 5.5%;"></td>
                        <td  style="width: 42%; padding: 0px;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">Время ожидания и бездействия</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt; ">'.$popup_tests_cache['time.over_time']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['time.over_time']['text'].'</font><br>
                        </td>
                        <td style="width: 6%;"></td>
                        <td style="width: 42%;"></td>
                        <td style="width: 4.5%;"></td>
                    </tr>
            ', 165);

        // 3. Тайм менеджмент - подробно
        // ====================================================================================
        $pdf->addPage(3);

        $pdf->writeTextBold($username, 3.5, 3.5, 21);

        $pdf->addPercentSmallInfo($data['time'][TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES], 176.5, 28.3);

        $pdf->writeTextCenterRegular(90, 10, 65, 33, 16,
            $popup_tests_cache['time.productive_time']['short_text']
        );//(очень высокий уровень)

        $pdf->addPercentMiddleInfo(
            $data['time'][TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES],
            82.1,
            54.3
        ); //Продуктивное время

        $pdf->addPercentMiddleInfo(
            $data['time'][TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES],
            185,
            54.3
        );//Не продуктивное время

        //Positive
        $x_positive = 33;
        $max_positive = $pdf->getMaxTimePositive($data['time']);

        //Документы 218
        $pdf->addTimeBarProductive($x_positive, 74.5, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS], $max_positive);

        //Встречи
        $pdf->addTimeBarProductive($x_positive, 85.5, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS], $max_positive);

        //Звонки
        $pdf->addTimeBarProductive($x_positive, 96.5, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS], $max_positive);

        //Почта
        $pdf->addTimeBarProductive($x_positive, 107, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL], $max_positive);

        //План
        $pdf->addTimeBarProductive($x_positive, 117.5, $data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING], $max_positive);

        //Negative
        $y_positive = 137;
        $max_negative = $pdf->getMaxTimeNegative($data['time']);

        //Документы
        $pdf->addTimeBarUnproductive($y_positive, 74.5, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS], $max_negative);

        //Встречи
        $pdf->addTimeBarUnproductive($y_positive, 85.5, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS], $max_negative);

        //Звонки
        $pdf->addTimeBarUnproductive($y_positive, 96.5, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS], $max_negative);

        //Почта
        $pdf->addTimeBarUnproductive($y_positive, 107, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL], $max_negative);

        //План
        $pdf->addTimeBarUnproductive($y_positive, 117.5, $data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING], $max_negative);

        // 4. Результативность
        $pdf->addPage(4);
        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->addPercentSmallInfo($data['performance']['total'], 133.8, 28);
        $pdf->writeTextCenterRegular(90, 10, 65, 33, 16, $popup_tests_cache['performance']['short_text']);//(очень высокий уровень)
        //Срочно
        $pdf->addUniversalBar(77, 54.8, $pdf->getPerformanceCategory($data['performance'], '0'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        //Высокий приоритет
        $pdf->addUniversalBar(77, 65.5, $pdf->getPerformanceCategory($data['performance'], '1'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        //Средний приоритет
        $pdf->addUniversalBar(77, 76.2, $pdf->getPerformanceCategory($data['performance'], '2'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        //Двухминутные задачи
        $pdf->addUniversalBar(77, 87.2, $pdf->getPerformanceCategory($data['performance'], '2_min'), 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);

        $pdf->writeHtml('
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">Срочно</font
                            >&nbsp;&nbsp;<font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['performance.urgent']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['performance.urgent']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">Высокий приоритет</font
                            >&nbsp;&nbsp;<font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['performance.high']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['performance.high']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">Средний приоритет</font
                            >&nbsp;&nbsp;<font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['performance.middle']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['performance.middle']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">Двухминутные задачи</font
                            >&nbsp;&nbsp;<font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['performance.two_minutes']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['performance.two_minutes']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
            ', 134);


        // 5. Управленческие навыки в общем
        $pdf->addPage(5);

        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->addPercentSmallInfo($data['management']['total'], 148, 27.7);
        $pdf->writeTextCenterRegular(90, 10, 58, 33, 16, $popup_tests_cache['management']['short_text']);//(очень высокий уровень)

        $pdf->addUniversalBar(77.7, 53.7, $data['management'][1]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//1
        $pdf->addUniversalBar(77.7, 64.5, $data['management'][2]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//2
        $pdf->addUniversalBar(77.7, 75.2, $data['management'][3]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//3


        if (Simulation::ASSESSMENT_VERSION_1 == $assessmentVersion) {
            // 6. Управленческие навыки - пункт 1 по версии v1
            $pdf->addPage(6);
            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addPercentBigInfo($data['management'][1]['total'], 3.4, 35.6);
            $pdf->writeTextCenterRegular(90, 10, 42, 41, 16, $popup_tests_cache['management.task_managment']['short_text']);//(очень высокий уровень)

            $pdf->addUniversalBar(77, 63, $data['management'][1]['1_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.1 positive
            $pdf->addUniversalBar(77, 73.6, $data['management'][1]['1_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.2 positive
            $pdf->addUniversalBar(77, 84.2, $data['management'][1]['1_4']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.3 positive

            $pdf->addUniversalBar(152, 63, $data['management'][1]['1_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.1 negative
            $pdf->addUniversalBar(152, 73.6, $data['management'][1]['1_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.2 negative
            $pdf->addUniversalBar(152, 84.2, $data['management'][1]['1_4']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.3 negative
            $pdf->addUniversalBar(152, 94.8, $data['management'][1]['1_5']['-'], 54.14, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_NEGATIVE);//1.4 negative

            $pdf->writeHtml('
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">1.1 Использование планирования в течение дня</font>
                            <font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.task_managment.day_planing']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.task_managment.day_planing']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">1.2 Правильное определение приоритетов задач при планировании</font>
                            <font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_priority_planing']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_priority_planing']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">1.3 Выполнение задач в соответствии с приоритетами</font>
                            <font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_priority_execution']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_priority_execution']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">1.4 Прерывание при выполнении задач</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_interruprion']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_interruprion']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
            ', 145);

        } elseif (Simulation::ASSESSMENT_VERSION_2 == $assessmentVersion) {
            // 6. Управленческие навыки - пункт 1 по версии v2
            $pdf->addPage(6);
            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addPercentBigInfo($data['management'][1]['total'], 3.4, 35.6);
            $pdf->writeTextCenterRegular(90, 10, 42, 41, 16, $popup_tests_cache['management.task_managment']['short_text']);//(очень высокий уровень)

            $pdf->addUniversalBar(77, 63, $data['management'][1]['1_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.1 positive
            $pdf->addUniversalBar(77, 73.6, $data['management'][1]['1_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.2 positive
            $pdf->addUniversalBar(77, 84.2, $data['management'][1]['1_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.3 positive

            $pdf->addUniversalBar(152, 63, $data['management'][1]['1_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.1 negative
            $pdf->addUniversalBar(152, 73.6, $data['management'][1]['1_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.2 negative
            $pdf->addUniversalBar(152, 84.2, $data['management'][1]['1_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.3 negative
            $pdf->addUniversalBar(152, 94.8, $data['management'][1]['1_4']['-'], 54.14, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_NEGATIVE);//1.4 negative

            $pdf->writeHtml('
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">1.1 Использование планирования в течение дня</font>
                            <font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.task_managment.day_planing']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.task_managment.day_planing']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">1.2 Правильное определение приоритетов задач при планировании</font>
                            <font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_priority_planing']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_priority_planing']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">1.3 Выполнение задач в соответствии с приоритетами</font>
                            <font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_priority_execution']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_priority_execution']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">1.4 Прерывание при выполнении задач</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_interruprion']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_interruprion']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
            ', 145);

        }

        // 7. Управленческие навыки - пункт 2
        $pdf->addPage(7);
        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->addPercentBigInfo($data['management'][2]['total'], 3.1, 36.3);
        $pdf->writeTextCenterRegular(90, 10, 10, 41, 16, $popup_tests_cache['management.people_managment']['short_text']);//(очень высокий уровень)

        $pdf->addUniversalBar(77, 63, $data['management'][2]['2_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.1 positive
        $pdf->addUniversalBar(77, 73.6, $data['management'][2]['2_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.2 positive
        $pdf->addUniversalBar(77, 84.2, $data['management'][2]['2_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.3 positive

        $pdf->addUniversalBar(152, 63, $data['management'][2]['2_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.1 negative
        $pdf->addUniversalBar(152, 73.6, $data['management'][2]['2_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.2 negative
        $pdf->addUniversalBar(152, 84.2, $data['management'][2]['2_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.3 negative

        $pdf->writeHtml('
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">2.1 Использование делегирования для управления объемом задач</font>
                            <font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.people_managment.delegation']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.people_managment.delegation']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">2.2 Управление ресурсами различной квалификации</font>
                            <font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.people_managment.resource_quality']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.people_managment.resource_quality']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">2.3 Использование обратной связи</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.people_managment.feedback']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.people_managment.feedback']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
            ', 135);

        // 8. Управленческие навыки - пункт 3
        $pdf->addPage(8);

        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->addPercentBigInfo($data['management'][3]['total'], 3, 35.8);
        $pdf->writeTextCenterRegular(90, 10, 23, 41, 16, $popup_tests_cache['management.communication_managment']['short_text']);//(очень высокий уровень)

        $pdf->addUniversalBar(77, 63.5, $data['management'][3]['3_1']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.1 positive
        $pdf->addUniversalBar(77, 72.7, $data['management'][3]['3_2']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.2 positive
        $pdf->addUniversalBar(77, 84, $data['management'][3]['3_3']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.3 positive
        $pdf->addUniversalBar(77, 94, $data['management'][3]['3_4']['+'], 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.4 positive

        $pdf->addUniversalBar(152, 63.5, $data['management'][3]['3_1']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.1 negative
        $pdf->addUniversalBar(152, 72.7, $data['management'][3]['3_2']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.2 negative
        $pdf->addUniversalBar(152, 84, $data['management'][3]['3_3']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.3 negative
        $pdf->addUniversalBar(152, 94, $data['management'][3]['3_4']['-'], 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.4 negative
        $pdf->writeHtml('
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">3.1 Оптимальное использование каналов коммуникации</font>
                            <font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.communication_managment.comunication_channel']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.communication_managment.comunication_channel']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">3.2 Эффективная работа с почтой</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.communication_managment.effective_mail']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.communication_managment.effective_mail']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">3.3 Эффективная работа со звонками</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.communication_managment.effective_calls']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.communication_managment.effective_calls']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">3.4 Эффективное управление встречами</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.communication_managment.effective_meetings']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.communication_managment.effective_meetings']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
            ', 145);

        $filename = $this->createFilename($simulation, 'results');

        if($save) {
            $pdf->saveOnDisk($path.'/'.$filename, false);
        } else {
            $pdf->renderOnBrowser($filename);
        }

        return $filename;
    }

    private function createBehavioursPDF(Simulation $simulation, $path, $save = true) {

        $data = unserialize($simulation->popup_tests_cache)['recommendation'];
        //var_dump($data);
        //exit;
        $username = $simulation->user->profile->firstname.' '.$simulation->user->profile->lastname;

        $titles = [
            '1' => '1. Управление задачами с учетом приоритетов',
            '1_1' => '1.1 Планирование рабочего дня',
            '1_2' => '1.2 Определение категории задачи (по матрице важно/срочно) при планировании',
            '1_3' => '1.3 Выполнение задач с учетом их категорий',
            '1_4' => '1.4 Завершения начатой задачи (следование приоритету и ориентация на результат)',
            '2' => '2. Управление людьми',
            '2_1' => '2.1 Использование делегирования',
            '2_2' => '2.2 Управление сотрудниками различной квалификации',
            '2_3' => '2.3 Использование обратной связи',
            '3' => '3. Управление коммуникациями',
            '3_1' => '3.1 Выбор наилучего канала коммуникации исходя из задач коммуникаций',
            '3_2' => '3.2 Эффективная работа с почтой',
            '3_3' => '3.3 Эффективная работа со звонками',
            '3_4' => '3.4 Эффективное управление встречами',
            '3_5_1' => 'Управление количеством и периодами времени, затраченного на почту',
            '3_5_2' => 'Эффективная обработка входящих писем в почте',
            '3_5_3' => 'Создание информативных и экономных исходящих писем',
            '3_5_4' => 'Управление количеством и периодами времени, затраченного на звонки',
            '3_5_5' => 'Эффективная обработка входящих звонков',
            '3_5_6' => 'Управление количеством и периодами времени, затраченного на встречи',
            '3_5_7' => 'Эффективный прием посетителей',
            '3_5_8' => 'Эффективная обработка результатов встречи',
        ];

        $sub_titles = [
            '3311' => '3_5_1',
            '3312' => '3_5_1',
            '3313' => '3_5_1',

            '3322' => '3_5_2',
            '3323' => '3_5_2',
            '3324' => '3_5_2',
            '3326' => '3_5_2',

            '3332' => '3_5_3',
            '3333' => '3_5_3',

            '341a8' => '3_5_4',

            '341b1' => '3_5_5',
            '341b5' => '3_5_5',
            '341b7' => '3_5_5',

            '351a1' => '3_5_6',
            '351a2' => '3_5_6',

            '351b1' => '3_5_7',
            '351b2' => '3_5_7',
            '351b3' => '3_5_7',

            '351c1' => '3_5_8',
            '351c2' => '3_5_8',
            '351c3' => '3_5_8'
        ];

        $pdf = new AssessmentPDF(true);
        $pdf->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->addEmptyPage();
        $pdf->writeTextBold($username, 33, 8.5, 21);
        $pdf->writeTextBold("Индивидуальный план развития", 45, 21, 21);

        $html = [];
        $Criteria = new CDbCriteria();
        $Criteria->order = 'code asc';
        $groups = $simulation->game_type->getLearningGoalGroups($Criteria);
        foreach($groups as $group) {
            $ul = [];
            foreach(LearningGoal::model()->findAllByAttributes(['learning_goal_group_id'=>$group->id]) as $learningGoal) {

                /* @var LearningGoal $learningGoal */
                foreach($learningGoal->heroBehaviours as $behaviour) {
                    //var_dump($behaviour->code);
                    /* @var HeroBehaviour $behaviour */
                    if(isset($data[$behaviour->code])) {
                        if($data[$behaviour->code]['short_text'] === '(хорошо)'){
                            continue;
                        }
                        $ul[$behaviour->code] = '<li><font face="dejavusans" style="font-size: 10pt;">'.$data[$behaviour->code]['text'].'</font></li>';
                    }
                }

            }
            ksort($ul);
            if(count($ul) === 0) { continue; }
            
            if(!isset($html[explode('_', $group->code)[0]])) {
                $html[explode('_', $group->code)[0]] = '<tr>
                            <td style="width: 15%;"></td>
                            <td style="width: 78%;"
                                ><font face="dejavusans" style="font-weight: bold;font-size: 15pt;">'.$titles[explode('_', $group->code)[0]].'</font>
                            </td>
                            <td style="width: 7%;"></td>
                          </tr>';
            }

            if(in_array($group->code, ['3_2', '3_3', '3_4'])) {
                $data2 = [];
                foreach($ul as $code => $text) {
                    if(isset($sub_titles[$code])) {
                        $data2[$sub_titles[$code]][] = $text;
                    }

                }

                $list = '';

                foreach ($data2 as $code => $li) {
                    $list.= '<table>
                        <tr>
                            <td>
                                <font face="dejavusans" style="font-weight: bold;font-size: 10pt;">'.$titles[$code].'</font
                            ></td>
                        </tr>
                        <tr>
                            <td>
                            <ul>'.implode('', $li).'</ul>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        </table>';
                }

                if (0 < count($data2)) {
                    $html[$group->code] = '<tr>
                            <td style="width: 15%;"></td>
                            <td style="width: 78%;"
                                ><font face="dejavusans" style="font-weight: bold;font-size: 12pt;">'.$titles[$group->code].'</font
                                ></td>
                            <td style="width: 7%;"></td>
                          </tr>
                          <tr>
                            <td style="width: 15%;"></td>
                            <td style="width: 78%;">
                            '. $list.'
                            </td>
                            <td style="width: 7%;"></td>
                          </tr>';
                } else {
                    $html[$group->code] = '<tr>
                            <td style="width: 15%;"></td>
                            <td style="width: 78%;"
                                ><font face="dejavusans" style="font-weight: bold;font-size: 12pt;">'.$titles[$group->code].'</font
                                >
                            </td>
                            <td style="width: 7%;"></td>
                          </tr>
                          <br>';
                }


            } else {
                // '1_1', '1_2', '1_3', '1_4', '2_1', '2_2', '2_3', '3_1', '3_5'
                $html[$group->code] = '<tr>
                        <td style="width: 15%;"></td>
                        <td style="width: 78%;"
                            ><font face="dejavusans" style="font-weight: bold; font-size: 12pt; ">'.$titles[$group->code].'</font>
                        </td>
                        <td style="width: 7%;"></td>
                      </tr>
                      <tr>
                        <td style="width: 15%;"></td>
                        <td style="width: 78%;"
                            ><ul>
                                '.implode('', $ul).'
                              </ul>
                        </td>
                        <td style="width: 7%;"></td>
                      </tr>
                      <br>';
            }
        }
        $pdf->writeHtml('&nbsp;&nbsp;&nbsp;&nbsp;'.implode('', $html), 35);

        $filename = $this->createFilename($simulation, 'plan_razvitiya');
        if($save) {
            $pdf->saveOnDisk($path.'/'.$filename, false);
        } else {
            $pdf->renderOnBrowser($filename);
        }

        return $filename;
    }

    private function createFilename(Simulation $simulation, $type) {

        $filename = '';
        $filename .= StringTools::CyToEnWithUppercase($simulation->user->profile->firstname);
        $filename .= '_'.StringTools::CyToEnWithUppercase($simulation->user->profile->lastname);
        if($simulation->invite->vacancy !== null) {
            $filename .= '_'.preg_replace("/[^a-zA-Z0-9]/", "", StringTools::CyToEnWithUppercase($simulation->invite->vacancy->label));
        }

        $filename .= '_'.$type.'_'.date('dmy', strtotime($simulation->end));

        return str_replace(' ', '_', $filename);
    }

} 