<?php

class PDFController extends SiteBaseController {

    public function actionSimulationDetailPDF()
    {

        $this->user = Yii::app()->user->data();
        if(null === $this->user && false === $this->user->isAuth()) {
            $this->redirect('/registration');
        }

        $simId = 11056; //$this->getParam('sim_id');
        //$simId = $this->getParam('sim_id');
        $assessmentVersion = 'v2';//$this->getParam('assessment_version');
        //$assessmentVersion = $this->getParam('assessment_version');


        /* @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);
        $isUser = $simulation->user_id === $this->user->id;
        $isOwner = $simulation->invite->owner_id === $this->user->id;
        $isAdmin = $this->user->isAdmin();

        if($isUser || $isOwner || $isAdmin) {
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
            $pdf->debug = true;
            $username = $simulation->user->profile->firstname.' '.$simulation->user->profile->lastname;

            $pdf->setImagesDir('simulation_details_'.$assessmentVersion.'/images/');

            $pdf->pdf->setCellHeightRatio(1);

        // 1. Спидометры и прочее
            $pdf->addPage(1);

            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addRatingPercentile(94, 35.6, $data['percentile']['total']);
            $pdf->addRatingOverall(86.6, 45.8, $data['overall']);
            $pdf->addSpeedometer(21, 107.2, $data['time']['total']);
            $pdf->addSpeedometer(89, 107.2, $data['performance']['total']);
            $pdf->addSpeedometer(158, 107.2, $data['management']['total']);

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
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">Сверхурочное</font><br
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

            //====================================================================================
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

        // 3. Результативность
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
                            >&nbsp;&nbsp;<font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['preformance.two_minutes']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['preformance.two_minutes']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
            ', 134);


        // 4. Управленческие навыки
            $pdf->addPage(5);

            $pdf->writeTextBold($username, 3.5, 3.5, 21);
            $pdf->addPercentSmallInfo($data['management']['total'], 148, 27.7);
            $pdf->writeTextCenterRegular(90, 10, 58, 33, 16, $popup_tests_cache['management']['short_text']);//(очень высокий уровень)

            $pdf->addUniversalBar(77.7, 53.7, $data['management'][1]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//1
            $pdf->addUniversalBar(77.7, 64.5, $data['management'][2]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//2
            $pdf->addUniversalBar(77.7, 75.2, $data['management'][3]['total'], 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//3


            if (Simulation::ASSESSMENT_VERSION_1 == $assessmentVersion) {
            // 5. Управленческие навыки - 1 по версии v1

            $pdf->addPage(6);
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
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">1.1 Использование планирования в течение дня</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.task_managment.day_planing']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.task_managment.day_planing']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">1.2 Правильное определение приоритетов задач при планировании</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_priority_planing']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_priority_planing']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">1.3 Выполнение задач в соответствии с приоритетами</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.task_managment.tasks_priority_execution']['short_text'].'</font><br
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
            ', 150);

            }

            // 6. Управленческие навыки - 2
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
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">2.1 Использование делегирования для управления объемом задач</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.people_managment.delegation']['short_text'].'</font><br
                            ><font style="font-size: 13pt;"></font><font face="dejavusans" style="font-size: 11pt;">'.$popup_tests_cache['management.people_managment.delegation']['text'].'</font><br>
                        </td>
                        <td style="width: 5%;"></td>
                    </tr>
                    <tr>
                        <td style="width: 35%;"></td>
                        <td style="width: 60%;"
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">2.2 Управление ресурсами различной квалификации</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.people_managment.resource_quality']['short_text'].'</font><br
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
            ', 140);

            // 7. Управленческие навыки - 3
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
                            ><font face="dejavusans" style="font-weight: bold;font-size: 13pt;">3.1 Оптимальное использование каналов коммуникации</font><br
                            ><font face="dejavusans" style="font-weight: bold;font-size: 11pt;">'.$popup_tests_cache['management.communication_managment.comunication_channel']['short_text'].'</font><br
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
            ', 150);

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