<?php
/**
 * Created by PhpStorm.
 * User: slavka
 * Date: 5/13/14
 * Time: 2:14 PM
 */

class AssessmentPockets_manager_1_UnitTest extends CDbTestCase {

    /**
     * Проверяем менеджерские навыки - они имеют комбинированные тексты "позитив+негатив"
     * 1. 1й карман - позитив, позитив по нулям
     * Негатив по нулям
     */
    public function testTextForInfoGraphic_1() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        // 1.Менеджерские навыки. - 1.Всё по нулям.
        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"0",
                             "1_1":{
                                "+":"0",
                                "-":"0"
                             },
                             "1_2":{
                                "+":"0",
                                "-":"0"
                             },
                             "1_3":{
                                "+":"0",
                                "-":"0"
                             },
                             "1_4":{
                                "+":"0",
                                "-":"0"
                             }
                          },
                          "2":{
                             "total":"0",
                             "2_1":{
                                "+":"0",
                                "-":"0"
                             },
                             "2_2":{
                                "+":"0",
                                "-":"0"
                             },
                             "2_3":{
                                "+":"0",
                                "-":"0"
                             }
                          },
                          "3":{
                             "total":"0",
                             "3_1":{
                                "+":"0",
                                "-":"0"
                             },
                             "3_2":{
                                "+":"0",
                                "-":"0"
                             },
                             "3_3":{
                                "+":"0",
                                "-":"0"
                             },
                             "3_4":{
                                "+":"0",
                                "-":"0"
                             }
                          },
                          "total":"0"
                       },
                       "performance":{
                          "0":"0",
                          "1":"0",
                          "2":"0",
                          "total":"0",
                          "2_min":"0"
                       },
                       "time":{
                          "total":"0",
                          "workday_overhead_duration":"0",
                          "time_spend_for_1st_priority_activities":"0",
                          "time_spend_for_non_priority_activities":"0",
                          "time_spend_for_inactivity":"0",
                          "1st_priority_documents":"0",
                          "1st_priority_meetings":"0",
                          "1st_priority_phone_calls":"0",
                          "1st_priority_mail":"0",
                          "1st_priority_planning":"0",
                          "non_priority_documents":"0",
                          "non_priority_meetings":"0",
                          "non_priority_phone_calls":"0",
                          "non_priority_mail":"0",
                          "non_priority_planning":"0",
                          "efficiency":"0"
                       },
                       "overall":"0",
                       "percentile":{
                          "total":"0"
                       },
                       "personal":{
                          "9":"0",
                          "10":"0",
                          "11":"0",
                          "12":"0",
                          "13":"0",
                          "14":"0",
                          "15":"0",
                          "16":"0"
                       }
                    }
                '), true
            )
        );

        $results = SimulationResultTextService::generate($simulation, 'popup');

        //print_r($results);

        $this->assertEquals(
            $results['management.task_managment.day_planing'],
            [
                'text'                => 'Было запланировано менее 60% или более 100% рабочего времени или менеджер не приступал к планированию в начале рабочего дня.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Было запланировано менее 60% или более 100% рабочего времени или менеджер не приступал к планированию в начале рабочего дня.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_planing'],
            [
                'text'                => 'Не учтены категории задач по матрице важно/срочно при их постановке в план.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Не учтены категории задач по матрице важно/срочно при их постановке в план.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_execution'],
            [
                'text'                => 'При выполнении задач в ходе дня менеджер многократно неверно интерпретирует категорию поступившей задачи и выполняет менее приоритетные задачи перед более приоритетными.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'При выполнении задач в ходе дня менеджер многократно неверно интерпретирует категорию поступившей задачи и выполняет менее приоритетные задачи перед более приоритетными.',
                'text_negative'       => 'При выполнении задач в ходе дня было допущено менее пяти грубых ошибок: задача очень низкой категории была выполнена ранее задачи очень высокого приоритета.',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_interruprion'],
            [
                'text'                => 'Менеджер редко прерывался (менее 8 раз) при выполнении задач высокой и средней категории, отвлекаясь на внешние раздражители и задачи низкой категории',
                'short_text'          => '(нет ошибок)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.delegation'],
            [
                'text'                => 'Были неправильно делегированы задачи, исходя из требуемой для их выполнения квалификации и загрузки сотрудников.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Были неправильно делегированы задачи, исходя из требуемой для их выполнения квалификации и загрузки сотрудников.',
                'text_negative'       => 'Грубых ошибок при делегировании допущено не было',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.resource_quality'],
            [
                'text'                => 'Менеджер не понял, что имеет дело с сотрудниками разной квалификации, не адаптировал свои методы управления и контроля для разных сотрудников.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Менеджер не понял, что имеет дело с сотрудниками разной квалификации, не адаптировал свои методы управления и контроля для разных сотрудников.',
                'text_negative'       => 'Грубых ошибок при управлении сотрудниками допущено не было',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.feedback'],
            [
                'text'                => 'Менеджер не использовал возможность дать сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Менеджер не использовал возможность дать сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'text_negative'       => 'Грубых ошибок при использовании обратной связи допущено не было',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.comunication_channel'],
            [
                'text'                => 'Многократно были выбраны и  использованы каналы коммуникаций, не самым оптимальным образом соответствующие специфике задачи, и/или неэкономные по времени.',
                'short_text'          => '(низкий уровень)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_mail'],
            [
                'text'                => 'Значимую часть времени менеджер провел в почте, читая и отвечая на письма низкого приоритета.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Значимую часть времени менеджер провел в почте, читая и отвечая на письма низкого приоритета.',
                'text_negative'       => 'Грубых ошибок при работе с почтой допущено не было',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_calls'],
            [
                'text'                => 'Менеджер отвечал на большую часть звонков, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'short_text'          => '(низкий уровень)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_meetings'],
            [
                'text'                => 'Менеджер соглашался на большую часть встреч, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Менеджер соглашался на большую часть встреч, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'text_negative'       => 'Грубых ошибок при управлении встречами допущено не было',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );
    }

    /**
     * Проверяем менеджерские навыки - они имеют комбинированные тексты "позитив+негатив"
     * 2. верхняя граница 1го кармана, позитив
     * Верхняя граница 1го кармана, негатив
     */
    public function testTextForInfoGraphic_2() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        // 1.Менеджерские навыки. - 1.Всё по нулям.
        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"0",
                             "1_1":{
                                "+":"39",
                                "-":"39"
                             },
                             "1_2":{
                                "+":"39",
                                "-":"39"
                             },
                             "1_3":{
                                "+":"59",
                                "-":"39"
                             },
                             "1_4":{
                                "+":"0",
                                "-":"39"
                             }
                          },
                          "2":{
                             "total":"0",
                             "2_1":{
                                "+":"39",
                                "-":"29"
                             },
                             "2_2":{
                                "+":"39",
                                "-":"29"
                             },
                             "2_3":{
                                "+":"39",
                                "-":"19"
                             }
                          },
                          "3":{
                             "total":"0",
                             "3_1":{
                                "+":"39",
                                "-":"39"
                             },
                             "3_2":{
                                "+":"32",
                                "-":"39"
                             },
                             "3_3":{
                                "+":"39",
                                "-":"39"
                             },
                             "3_4":{
                                "+":"39",
                                "-":"39"
                             }
                          },
                          "total":"0"
                       },
                       "performance":{
                          "0":"0",
                          "1":"0",
                          "2":"0",
                          "total":"0",
                          "2_min":"0"
                       },
                       "time":{
                          "total":"0",
                          "workday_overhead_duration":"0",
                          "time_spend_for_1st_priority_activities":"0",
                          "time_spend_for_non_priority_activities":"0",
                          "time_spend_for_inactivity":"0",
                          "1st_priority_documents":"0",
                          "1st_priority_meetings":"0",
                          "1st_priority_phone_calls":"0",
                          "1st_priority_mail":"0",
                          "1st_priority_planning":"0",
                          "non_priority_documents":"0",
                          "non_priority_meetings":"0",
                          "non_priority_phone_calls":"0",
                          "non_priority_mail":"0",
                          "non_priority_planning":"0",
                          "efficiency":"0"
                       },
                       "overall":"0",
                       "percentile":{
                          "total":"0"
                       },
                       "personal":{
                          "9":"0",
                          "10":"0",
                          "11":"0",
                          "12":"0",
                          "13":"0",
                          "14":"0",
                          "15":"0",
                          "16":"0"
                       }
                    }
                '), true
            )
        );

        $results = SimulationResultTextService::generate($simulation, 'popup');

        //print_r($results);

        $this->assertEquals(
            $results['management.task_managment.day_planing'],
            [
                'text'                => 'Было запланировано менее 60% или более 100% рабочего времени или менеджер не приступал к планированию в начале рабочего дня.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Было запланировано менее 60% или более 100% рабочего времени или менеджер не приступал к планированию в начале рабочего дня.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_planing'],
            [
                'text'                => 'Не учтены категории задач по матрице важно/срочно при их постановке в план.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Не учтены категории задач по матрице важно/срочно при их постановке в план.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_execution'],
            [
                'text'                => 'При выполнении задач в ходе дня менеджер многократно неверно интерпретирует категорию поступившей задачи и выполняет менее приоритетные задачи перед более приоритетными.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'При выполнении задач в ходе дня менеджер многократно неверно интерпретирует категорию поступившей задачи и выполняет менее приоритетные задачи перед более приоритетными.',
                'text_negative'       => 'При выполнении задач в ходе дня было допущено менее пяти грубых ошибок: задача очень низкой категории была выполнена ранее задачи очень высокого приоритета.',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_interruprion'],
            [
                'text'                => 'Менеджер редко прерывался (менее 8 раз) при выполнении задач высокой и средней категории, отвлекаясь на внешние раздражители и задачи низкой категории',
                'short_text'          => '(нет ошибок)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.delegation'],
            [
                'text'                => 'Были неправильно делегированы задачи, исходя из требуемой для их выполнения квалификации и загрузки сотрудников.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Были неправильно делегированы задачи, исходя из требуемой для их выполнения квалификации и загрузки сотрудников.',
                'text_negative'       => 'Грубых ошибок при делегировании допущено не было',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.resource_quality'],
            [
                'text'                => 'Менеджер не понял, что имеет дело с сотрудниками разной квалификации, не адаптировал свои методы управления и контроля для разных сотрудников.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Менеджер не понял, что имеет дело с сотрудниками разной квалификации, не адаптировал свои методы управления и контроля для разных сотрудников.',
                'text_negative'       => 'Грубых ошибок при управлении сотрудниками допущено не было',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.feedback'],
            [
                'text'                => 'Менеджер не использовал возможность дать сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Менеджер не использовал возможность дать сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'text_negative'       => 'Грубых ошибок при использовании обратной связи допущено не было',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.comunication_channel'],
            [
                'text'                => 'Многократно были выбраны и  использованы каналы коммуникаций, не самым оптимальным образом соответствующие специфике задачи, и/или неэкономные по времени.',
                'short_text'          => '(низкий уровень)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_mail'],
            [
                'text'                => 'Значимую часть времени менеджер провел в почте, читая и отвечая на письма низкого приоритета.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Значимую часть времени менеджер провел в почте, читая и отвечая на письма низкого приоритета.',
                'text_negative'       => 'Грубых ошибок при работе с почтой допущено не было',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_calls'],
            [
                'text'                => 'Менеджер отвечал на большую часть звонков, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'short_text'          => '(низкий уровень)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_meetings'],
            [
                'text'                => 'Менеджер соглашался на большую часть встреч, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'short_text'          => '(низкий уровень)',
                'text_positive'       => 'Менеджер соглашался на большую часть встреч, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'text_negative'       => 'Грубых ошибок при управлении встречами допущено не было',
                'short_text_positive' => 'низкий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );
    }

    /**
     * Проверяем менеджерские навыки - они имеют комбинированные тексты "позитив+негатив"
     * 3. нижняя граница 2го кармана, позитив
     * Негатив по нулям
     */
    public function testTextForInfoGraphic_3() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        // 1.Менеджерские навыки. - 1.Всё по нулям.
        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"0",
                             "1_1":{
                                "+":"40",
                                "-":"0"
                             },
                             "1_2":{
                                "+":"40",
                                "-":"0"
                             },
                             "1_3":{
                                "+":"60",
                                "-":"0"
                             },
                             "1_4":{
                                "+":"0",
                                "-":"0"
                             }
                          },
                          "2":{
                             "total":"0",
                             "2_1":{
                                "+":"40",
                                "-":"0"
                             },
                             "2_2":{
                                "+":"40",
                                "-":"0"
                             },
                             "2_3":{
                                "+":"40",
                                "-":"0"
                             }
                          },
                          "3":{
                             "total":"0",
                             "3_1":{
                                "+":"40",
                                "-":"0"
                             },
                             "3_2":{
                                "+":"33",
                                "-":"0"
                             },
                             "3_3":{
                                "+":"40",
                                "-":"0"
                             },
                             "3_4":{
                                "+":"40",
                                "-":"0"
                             }
                          },
                          "total":"0"
                       },
                       "performance":{
                          "0":"0",
                          "1":"0",
                          "2":"0",
                          "total":"0",
                          "2_min":"0"
                       },
                       "time":{
                          "total":"0",
                          "workday_overhead_duration":"0",
                          "time_spend_for_1st_priority_activities":"0",
                          "time_spend_for_non_priority_activities":"0",
                          "time_spend_for_inactivity":"0",
                          "1st_priority_documents":"0",
                          "1st_priority_meetings":"0",
                          "1st_priority_phone_calls":"0",
                          "1st_priority_mail":"0",
                          "1st_priority_planning":"0",
                          "non_priority_documents":"0",
                          "non_priority_meetings":"0",
                          "non_priority_phone_calls":"0",
                          "non_priority_mail":"0",
                          "non_priority_planning":"0",
                          "efficiency":"0"
                       },
                       "overall":"0",
                       "percentile":{
                          "total":"0"
                       },
                       "personal":{
                          "9":"0",
                          "10":"0",
                          "11":"0",
                          "12":"0",
                          "13":"0",
                          "14":"0",
                          "15":"0",
                          "16":"0"
                       }
                    }
                '), true
            )
        );

        $results = SimulationResultTextService::generate($simulation, 'popup');

//        print_r($results);
//        die;

        $this->assertEquals(
            $results['management.task_managment.day_planing'],
            [
                'text'                => 'Было запланировано менее 60% или более 100% рабочего времени или менеджер не приступал к планированию в начале рабочего дня.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Было запланировано менее 60% или более 100% рабочего времени или менеджер не приступал к планированию в начале рабочего дня.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_planing'],
            [
                'text'                => 'Не учтены категории задач по матрице важно/срочно при их постановке в план.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Не учтены категории задач по матрице важно/срочно при их постановке в план.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_execution'],
            [
                'text'                => 'При выполнении задач в ходе дня менеджер многократно неверно интерпретирует категорию поступившей задачи и выполняет менее приоритетные задачи перед более приоритетными.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'При выполнении задач в ходе дня менеджер многократно неверно интерпретирует категорию поступившей задачи и выполняет менее приоритетные задачи перед более приоритетными.',
                'text_negative'       => 'При выполнении задач в ходе дня было допущено менее пяти грубых ошибок: задача очень низкой категории была выполнена ранее задачи очень высокого приоритета.',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 1.4.-.
        $this->assertEquals(
            $results['management.task_managment.tasks_interruprion'],
            [
                'text'                => 'Менеджер редко прерывался (менее 8 раз) при выполнении задач высокой и средней категории, отвлекаясь на внешние раздражители и задачи низкой категории',
                'short_text'          => '(нет ошибок)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.delegation'],
            [
                'text'                => 'Были неправильно делегированы задачи, исходя из требуемой для их выполнения квалификации и загрузки сотрудников.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Были неправильно делегированы задачи, исходя из требуемой для их выполнения квалификации и загрузки сотрудников.',
                'text_negative'       => 'Грубых ошибок при делегировании допущено не было',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.resource_quality'],
            [
                'text'                => 'Менеджер не понял, что имеет дело с сотрудниками разной квалификации, не адаптировал свои методы управления и контроля для разных сотрудников.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Менеджер не понял, что имеет дело с сотрудниками разной квалификации, не адаптировал свои методы управления и контроля для разных сотрудников.',
                'text_negative'       => 'Грубых ошибок при управлении сотрудниками допущено не было',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.feedback'],
            [
                'text'                => 'Менеджер не использовал возможность дать сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Менеджер не использовал возможность дать сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'text_negative'       => 'Грубых ошибок при использовании обратной связи допущено не было',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.1.+.
        $this->assertEquals(
            $results['management.communication_managment.comunication_channel'],
            [
                'text'                => 'Многократно были выбраны и  использованы каналы коммуникаций, не самым оптимальным образом соответствующие специфике задачи, и/или неэкономные по времени.',
                'short_text'          => '(средний уровень)',
                'pocket'               => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_mail'],
            [
                'text'                => 'Значимую часть времени менеджер провел в почте, читая и отвечая на письма низкого приоритета.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Значимую часть времени менеджер провел в почте, читая и отвечая на письма низкого приоритета.',
                'text_negative'       => 'Грубых ошибок при работе с почтой допущено не было',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.3.+.
        $this->assertEquals(
            $results['management.communication_managment.effective_calls'],
            [
                'text'                => 'Менеджер отвечал на большую часть звонков, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'short_text'          => '(средний уровень)',
                'pocket'               => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_meetings'],
            [
                'text'                => 'Менеджер соглашался на большую часть встреч, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Менеджер соглашался на большую часть встреч, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'text_negative'       => 'Грубых ошибок при управлении встречами допущено не было',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );
    }

    /**
     * Проверяем менеджерские навыки - они имеют комбинированные тексты "позитив+негатив"
     * 4. верхняя граница 2го кармана, позитив
     * Негатив по нулям
     */
    public function testTextForInfoGraphic_4() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        // 1.Менеджерские навыки. - 1.Всё по нулям.
        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"0",
                             "1_1":{
                                "+":"59",
                                "-":"0"
                             },
                             "1_2":{
                                "+":"59",
                                "-":"0"
                             },
                             "1_3":{
                                "+":"69",
                                "-":"0"
                             },
                             "1_4":{
                                "+":"0",
                                "-":"0"
                             }
                          },
                          "2":{
                             "total":"0",
                             "2_1":{
                                "+":"59",
                                "-":"0"
                             },
                             "2_2":{
                                "+":"59",
                                "-":"0"
                             },
                             "2_3":{
                                "+":"59",
                                "-":"0"
                             }
                          },
                          "3":{
                             "total":"0",
                             "3_1":{
                                "+":"59",
                                "-":"0"
                             },
                             "3_2":{
                                "+":"49",
                                "-":"0"
                             },
                             "3_3":{
                                "+":"59",
                                "-":"0"
                             },
                             "3_4":{
                                "+":"59",
                                "-":"0"
                             }
                          },
                          "total":"0"
                       },
                       "performance":{
                          "0":"0",
                          "1":"0",
                          "2":"0",
                          "total":"0",
                          "2_min":"0"
                       },
                       "time":{
                          "total":"0",
                          "workday_overhead_duration":"0",
                          "time_spend_for_1st_priority_activities":"0",
                          "time_spend_for_non_priority_activities":"0",
                          "time_spend_for_inactivity":"0",
                          "1st_priority_documents":"0",
                          "1st_priority_meetings":"0",
                          "1st_priority_phone_calls":"0",
                          "1st_priority_mail":"0",
                          "1st_priority_planning":"0",
                          "non_priority_documents":"0",
                          "non_priority_meetings":"0",
                          "non_priority_phone_calls":"0",
                          "non_priority_mail":"0",
                          "non_priority_planning":"0",
                          "efficiency":"0"
                       },
                       "overall":"0",
                       "percentile":{
                          "total":"0"
                       },
                       "personal":{
                          "9":"0",
                          "10":"0",
                          "11":"0",
                          "12":"0",
                          "13":"0",
                          "14":"0",
                          "15":"0",
                          "16":"0"
                       }
                    }
                '), true
            )
        );

        $results = SimulationResultTextService::generate($simulation, 'popup');

//        print_r($results);
//        die;

        $this->assertEquals(
            $results['management.task_managment.day_planing'],
            [
                'text'                => 'Было запланировано менее 60% или более 100% рабочего времени или менеджер не приступал к планированию в начале рабочего дня.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Было запланировано менее 60% или более 100% рабочего времени или менеджер не приступал к планированию в начале рабочего дня.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_planing'],
            [
                'text'                => 'Не учтены категории задач по матрице важно/срочно при их постановке в план.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Не учтены категории задач по матрице важно/срочно при их постановке в план.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_execution'],
            [
                'text'                => 'При выполнении задач в ходе дня менеджер многократно неверно интерпретирует категорию поступившей задачи и выполняет менее приоритетные задачи перед более приоритетными.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'При выполнении задач в ходе дня менеджер многократно неверно интерпретирует категорию поступившей задачи и выполняет менее приоритетные задачи перед более приоритетными.',
                'text_negative'       => 'При выполнении задач в ходе дня было допущено менее пяти грубых ошибок: задача очень низкой категории была выполнена ранее задачи очень высокого приоритета.',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 1.4.-.
        $this->assertEquals(
            $results['management.task_managment.tasks_interruprion'],
            [
                'text'                => 'Менеджер редко прерывался (менее 8 раз) при выполнении задач высокой и средней категории, отвлекаясь на внешние раздражители и задачи низкой категории',
                'short_text'          => '(нет ошибок)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.delegation'],
            [
                'text'                => 'Были неправильно делегированы задачи, исходя из требуемой для их выполнения квалификации и загрузки сотрудников.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Были неправильно делегированы задачи, исходя из требуемой для их выполнения квалификации и загрузки сотрудников.',
                'text_negative'       => 'Грубых ошибок при делегировании допущено не было',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.resource_quality'],
            [
                'text'                => 'Менеджер не понял, что имеет дело с сотрудниками разной квалификации, не адаптировал свои методы управления и контроля для разных сотрудников.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Менеджер не понял, что имеет дело с сотрудниками разной квалификации, не адаптировал свои методы управления и контроля для разных сотрудников.',
                'text_negative'       => 'Грубых ошибок при управлении сотрудниками допущено не было',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.feedback'],
            [
                'text'                => 'Менеджер не использовал возможность дать сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Менеджер не использовал возможность дать сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'text_negative'       => 'Грубых ошибок при использовании обратной связи допущено не было',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.1.+.
        $this->assertEquals(
            $results['management.communication_managment.comunication_channel'],
            [
                'text'                => 'Многократно были выбраны и  использованы каналы коммуникаций, не самым оптимальным образом соответствующие специфике задачи, и/или неэкономные по времени.',
                'short_text'          => '(средний уровень)',
                'pocket'               => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_mail'],
            [
                'text'                => 'Значимую часть времени менеджер провел в почте, читая и отвечая на письма низкого приоритета.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Значимую часть времени менеджер провел в почте, читая и отвечая на письма низкого приоритета.',
                'text_negative'       => 'Грубых ошибок при работе с почтой допущено не было',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.3.+.
        $this->assertEquals(
            $results['management.communication_managment.effective_calls'],
            [
                'text'                => 'Менеджер отвечал на большую часть звонков, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'short_text'          => '(средний уровень)',
                'pocket'               => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_meetings'],
            [
                'text'                => 'Менеджер соглашался на большую часть встреч, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'short_text'          => '(средний уровень)',
                'text_positive'       => 'Менеджер соглашался на большую часть встреч, не учитывая их категорию. Надолго оставался во второстепенном диалоге даже при наличии выхода из него.',
                'text_negative'       => 'Грубых ошибок при управлении встречами допущено не было',
                'short_text_positive' => 'средний уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );
    }

    /**
     * Проверяем менеджерские навыки - они имеют комбинированные тексты "позитив+негатив"
     * 5. нижняя граница 3го кармана, позитив
     * Негатив по нулям
     */
    public function testTextForInfoGraphic_5() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        // 1.Менеджерские навыки. - 1.Всё по нулям.
        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"0",
                             "1_1":{
                                "+":"60",
                                "-":"0"
                             },
                             "1_2":{
                                "+":"60",
                                "-":"0"
                             },
                             "1_3":{
                                "+":"70",
                                "-":"0"
                             },
                             "1_4":{
                                "+":"0",
                                "-":"0"
                             }
                          },
                          "2":{
                             "total":"0",
                             "2_1":{
                                "+":"60",
                                "-":"0"
                             },
                             "2_2":{
                                "+":"60",
                                "-":"0"
                             },
                             "2_3":{
                                "+":"60",
                                "-":"0"
                             }
                          },
                          "3":{
                             "total":"0",
                             "3_1":{
                                "+":"60",
                                "-":"0"
                             },
                             "3_2":{
                                "+":"50",
                                "-":"0"
                             },
                             "3_3":{
                                "+":"60",
                                "-":"0"
                             },
                             "3_4":{
                                "+":"60",
                                "-":"0"
                             }
                          },
                          "total":"0"
                       },
                       "performance":{
                          "0":"0",
                          "1":"0",
                          "2":"0",
                          "total":"0",
                          "2_min":"0"
                       },
                       "time":{
                          "total":"0",
                          "workday_overhead_duration":"0",
                          "time_spend_for_1st_priority_activities":"0",
                          "time_spend_for_non_priority_activities":"0",
                          "time_spend_for_inactivity":"0",
                          "1st_priority_documents":"0",
                          "1st_priority_meetings":"0",
                          "1st_priority_phone_calls":"0",
                          "1st_priority_mail":"0",
                          "1st_priority_planning":"0",
                          "non_priority_documents":"0",
                          "non_priority_meetings":"0",
                          "non_priority_phone_calls":"0",
                          "non_priority_mail":"0",
                          "non_priority_planning":"0",
                          "efficiency":"0"
                       },
                       "overall":"0",
                       "percentile":{
                          "total":"0"
                       },
                       "personal":{
                          "9":"0",
                          "10":"0",
                          "11":"0",
                          "12":"0",
                          "13":"0",
                          "14":"0",
                          "15":"0",
                          "16":"0"
                       }
                    }
                '), true
            )
        );

        $results = SimulationResultTextService::generate($simulation, 'popup');

//        print_r($results);
//        die;

        $this->assertEquals(
            $results['management.task_managment.day_planing'],
            [
                'text'                => 'Менеджер запланировал свой рабочий день утром, было запланировано более 60% и менее 80% рабочего времени с временными интервалами между задачами.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Менеджер запланировал свой рабочий день утром, было запланировано более 60% и менее 80% рабочего времени с временными интервалами между задачами.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_planing'],
            [
                'text'                => 'В целом задачи поставлены в план с учетом их категорий по матрице важно/срочно.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'В целом задачи поставлены в план с учетом их категорий по матрице важно/срочно.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_execution'],
            [
                'text'                => 'При выполнении задач в ходе дня менеджер многократно верно интерпретирует категорию поступившей задачи и выполняет более приоритетные задачи перед менее приоритетными.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'При выполнении задач в ходе дня менеджер многократно верно интерпретирует категорию поступившей задачи и выполняет более приоритетные задачи перед менее приоритетными.',
                'text_negative'       => 'При выполнении задач в ходе дня было допущено менее пяти грубых ошибок: задача очень низкой категории была выполнена ранее задачи очень высокого приоритета.',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 1.4.-.
        $this->assertEquals(
            $results['management.task_managment.tasks_interruprion'],
            [
                'text'                => 'Менеджер редко прерывался (менее 8 раз) при выполнении задач высокой и средней категории, отвлекаясь на внешние раздражители и задачи низкой категории',
                'short_text'          => '(нет ошибок)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.delegation'],
            [
                'text'                => 'Были корректно определены и делегированы задачи, корректно интерпретирована и использована информация о команде.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Были корректно определены и делегированы задачи, корректно интерпретирована и использована информация о команде.',
                'text_negative'       => 'Грубых ошибок при делегировании допущено не было',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.resource_quality'],
            [
                'text'                => 'Менеджер усвоил и корректно использовал информацию об уровне квалификации своих сотрудников, использовал контроль по результату для профессионального сотрудника и промежуточный контроль для слабого сотрудника.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Менеджер усвоил и корректно использовал информацию об уровне квалификации своих сотрудников, использовал контроль по результату для профессионального сотрудника и промежуточный контроль для слабого сотрудника.',
                'text_negative'       => 'Грубых ошибок при управлении сотрудниками допущено не было',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.feedback'],
            [
                'text'                => 'Менеджер давал сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Менеджер давал сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'text_negative'       => 'Грубых ошибок при использовании обратной связи допущено не было',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.1.+.
        $this->assertEquals(
            $results['management.communication_managment.comunication_channel'],
            [
                'text'                => 'Многократно были выбраны и  использованы каналы коммуникаций, наилучшим образом соответствующие специфике задачи, и/или экономные по времени.',
                'short_text'          => '(высокий уровень)',
                'pocket'               => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_mail'],
            [
                'text'                => 'Менеджер читал и отвечал на письма высокого и среднего приоритета.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Менеджер читал и отвечал на письма высокого и среднего приоритета.',
                'text_negative'       => 'Грубых ошибок при работе с почтой допущено не было',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.3.+.
        $this->assertEquals(
            $results['management.communication_managment.effective_calls'],
            [
                'text'                => 'Менеджер избирательно отвечал и совершал звонки, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'short_text'          => '(высокий уровень)',
                'pocket'               => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_meetings'],
            [
                'text'                => 'Менеджер избирательно соглашался на встречи, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Менеджер избирательно соглашался на встречи, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'text_negative'       => 'Грубых ошибок при управлении встречами допущено не было',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );
    }

    /**
     * Проверяем менеджерские навыки - они имеют комбинированные тексты "позитив+негатив"
     * 6. верхняя граница 3го кармана, позитив
     * Негатив по нулям
     */
    public function testTextForInfoGraphic_6() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        // 1.Менеджерские навыки. - 1.Всё по нулям.
        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"0",
                             "1_1":{
                                "+":"79",
                                "-":"0"
                             },
                             "1_2":{
                                "+":"79",
                                "-":"0"
                             },
                             "1_3":{
                                "+":"79",
                                "-":"0"
                             },
                             "1_4":{
                                "+":"0",
                                "-":"0"
                             }
                          },
                          "2":{
                             "total":"0",
                             "2_1":{
                                "+":"79",
                                "-":"0"
                             },
                             "2_2":{
                                "+":"79",
                                "-":"0"
                             },
                             "2_3":{
                                "+":"79",
                                "-":"0"
                             }
                          },
                          "3":{
                             "total":"0",
                             "3_1":{
                                "+":"79",
                                "-":"0"
                             },
                             "3_2":{
                                "+":"74",
                                "-":"0"
                             },
                             "3_3":{
                                "+":"79",
                                "-":"0"
                             },
                             "3_4":{
                                "+":"79",
                                "-":"0"
                             }
                          },
                          "total":"0"
                       },
                       "performance":{
                          "0":"0",
                          "1":"0",
                          "2":"0",
                          "total":"0",
                          "2_min":"0"
                       },
                       "time":{
                          "total":"0",
                          "workday_overhead_duration":"0",
                          "time_spend_for_1st_priority_activities":"0",
                          "time_spend_for_non_priority_activities":"0",
                          "time_spend_for_inactivity":"0",
                          "1st_priority_documents":"0",
                          "1st_priority_meetings":"0",
                          "1st_priority_phone_calls":"0",
                          "1st_priority_mail":"0",
                          "1st_priority_planning":"0",
                          "non_priority_documents":"0",
                          "non_priority_meetings":"0",
                          "non_priority_phone_calls":"0",
                          "non_priority_mail":"0",
                          "non_priority_planning":"0",
                          "efficiency":"0"
                       },
                       "overall":"0",
                       "percentile":{
                          "total":"0"
                       },
                       "personal":{
                          "9":"0",
                          "10":"0",
                          "11":"0",
                          "12":"0",
                          "13":"0",
                          "14":"0",
                          "15":"0",
                          "16":"0"
                       }
                    }
                '), true
            )
        );

        $results = SimulationResultTextService::generate($simulation, 'popup');

//        print_r($results);
//        die;

        $this->assertEquals(
            $results['management.task_managment.day_planing'],
            [
                'text'                => 'Менеджер запланировал свой рабочий день утром, было запланировано более 60% и менее 80% рабочего времени с временными интервалами между задачами.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Менеджер запланировал свой рабочий день утром, было запланировано более 60% и менее 80% рабочего времени с временными интервалами между задачами.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_planing'],
            [
                'text'                => 'В целом задачи поставлены в план с учетом их категорий по матрице важно/срочно.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'В целом задачи поставлены в план с учетом их категорий по матрице важно/срочно.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_execution'],
            [
                'text'                => 'При выполнении задач в ходе дня менеджер многократно верно интерпретирует категорию поступившей задачи и выполняет более приоритетные задачи перед менее приоритетными.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'При выполнении задач в ходе дня менеджер многократно верно интерпретирует категорию поступившей задачи и выполняет более приоритетные задачи перед менее приоритетными.',
                'text_negative'       => 'При выполнении задач в ходе дня было допущено менее пяти грубых ошибок: задача очень низкой категории была выполнена ранее задачи очень высокого приоритета.',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 1.4.-.
        $this->assertEquals(
            $results['management.task_managment.tasks_interruprion'],
            [
                'text'                => 'Менеджер редко прерывался (менее 8 раз) при выполнении задач высокой и средней категории, отвлекаясь на внешние раздражители и задачи низкой категории',
                'short_text'          => '(нет ошибок)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.delegation'],
            [
                'text'                => 'Были корректно определены и делегированы задачи, корректно интерпретирована и использована информация о команде.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Были корректно определены и делегированы задачи, корректно интерпретирована и использована информация о команде.',
                'text_negative'       => 'Грубых ошибок при делегировании допущено не было',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.resource_quality'],
            [
                'text'                => 'Менеджер усвоил и корректно использовал информацию об уровне квалификации своих сотрудников, использовал контроль по результату для профессионального сотрудника и промежуточный контроль для слабого сотрудника.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Менеджер усвоил и корректно использовал информацию об уровне квалификации своих сотрудников, использовал контроль по результату для профессионального сотрудника и промежуточный контроль для слабого сотрудника.',
                'text_negative'       => 'Грубых ошибок при управлении сотрудниками допущено не было',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.feedback'],
            [
                'text'                => 'Менеджер давал сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Менеджер давал сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'text_negative'       => 'Грубых ошибок при использовании обратной связи допущено не было',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.1.+.
        $this->assertEquals(
            $results['management.communication_managment.comunication_channel'],
            [
                'text'                => 'Многократно были выбраны и  использованы каналы коммуникаций, наилучшим образом соответствующие специфике задачи, и/или экономные по времени.',
                'short_text'          => '(высокий уровень)',
                'pocket'               => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_mail'],
            [
                'text'                => 'Менеджер читал и отвечал на письма высокого и среднего приоритета.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Менеджер читал и отвечал на письма высокого и среднего приоритета.',
                'text_negative'       => 'Грубых ошибок при работе с почтой допущено не было',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.3.+.
        $this->assertEquals(
            $results['management.communication_managment.effective_calls'],
            [
                'text'                => 'Менеджер избирательно отвечал и совершал звонки, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'short_text'          => '(высокий уровень)',
                'pocket'               => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_meetings'],
            [
                'text'                => 'Менеджер избирательно соглашался на встречи, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'short_text'          => '(высокий уровень)',
                'text_positive'       => 'Менеджер избирательно соглашался на встречи, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'text_negative'       => 'Грубых ошибок при управлении встречами допущено не было',
                'short_text_positive' => 'высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );
    }

    /**
     * Проверяем менеджерские навыки - они имеют комбинированные тексты "позитив+негатив"
     * 7. нижняя граница 4го кармана, позитив
     * Негатив по нулям
     */
    public function testTextForInfoGraphic_7() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        // 1.Менеджерские навыки. - 1.Всё по нулям.
        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"0",
                             "1_1":{
                                "+":"80",
                                "-":"0"
                             },
                             "1_2":{
                                "+":"80",
                                "-":"0"
                             },
                             "1_3":{
                                "+":"80",
                                "-":"0"
                             },
                             "1_4":{
                                "+":"0",
                                "-":"0"
                             }
                          },
                          "2":{
                             "total":"0",
                             "2_1":{
                                "+":"80",
                                "-":"0"
                             },
                             "2_2":{
                                "+":"80",
                                "-":"0"
                             },
                             "2_3":{
                                "+":"80",
                                "-":"0"
                             }
                          },
                          "3":{
                             "total":"0",
                             "3_1":{
                                "+":"80",
                                "-":"0"
                             },
                             "3_2":{
                                "+":"75",
                                "-":"0"
                             },
                             "3_3":{
                                "+":"80",
                                "-":"0"
                             },
                             "3_4":{
                                "+":"80",
                                "-":"0"
                             }
                          },
                          "total":"0"
                       },
                       "performance":{
                          "0":"0",
                          "1":"0",
                          "2":"0",
                          "total":"0",
                          "2_min":"0"
                       },
                       "time":{
                          "total":"0",
                          "workday_overhead_duration":"0",
                          "time_spend_for_1st_priority_activities":"0",
                          "time_spend_for_non_priority_activities":"0",
                          "time_spend_for_inactivity":"0",
                          "1st_priority_documents":"0",
                          "1st_priority_meetings":"0",
                          "1st_priority_phone_calls":"0",
                          "1st_priority_mail":"0",
                          "1st_priority_planning":"0",
                          "non_priority_documents":"0",
                          "non_priority_meetings":"0",
                          "non_priority_phone_calls":"0",
                          "non_priority_mail":"0",
                          "non_priority_planning":"0",
                          "efficiency":"0"
                       },
                       "overall":"0",
                       "percentile":{
                          "total":"0"
                       },
                       "personal":{
                          "9":"0",
                          "10":"0",
                          "11":"0",
                          "12":"0",
                          "13":"0",
                          "14":"0",
                          "15":"0",
                          "16":"0"
                       }
                    }
                '), true
            )
        );

        $results = SimulationResultTextService::generate($simulation, 'popup');

//        print_r($results);
//        die;

        $this->assertEquals(
            $results['management.task_managment.day_planing'],
            [
                'text'                => 'Менеджер запланировал свой рабочий день утром, было запланировано более 60% и менее 80% рабочего времени с временными интервалами между задачами.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Менеджер запланировал свой рабочий день утром, было запланировано более 60% и менее 80% рабочего времени с временными интервалами между задачами.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_planing'],
            [
                'text'                => 'В целом задачи поставлены в план с учетом их категорий по матрице важно/срочно.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'В целом задачи поставлены в план с учетом их категорий по матрице важно/срочно.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_execution'],
            [
                'text'                => 'При выполнении задач в ходе дня менеджер многократно верно интерпретирует категорию поступившей задачи и выполняет более приоритетные задачи перед менее приоритетными.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'При выполнении задач в ходе дня менеджер многократно верно интерпретирует категорию поступившей задачи и выполняет более приоритетные задачи перед менее приоритетными.',
                'text_negative'       => 'При выполнении задач в ходе дня было допущено менее пяти грубых ошибок: задача очень низкой категории была выполнена ранее задачи очень высокого приоритета.',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 1.4.-.
        $this->assertEquals(
            $results['management.task_managment.tasks_interruprion'],
            [
                'text'                => 'Менеджер редко прерывался (менее 8 раз) при выполнении задач высокой и средней категории, отвлекаясь на внешние раздражители и задачи низкой категории',
                'short_text'          => '(нет ошибок)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.delegation'],
            [
                'text'                => 'Были корректно определены и делегированы задачи, корректно интерпретирована и использована информация о команде.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Были корректно определены и делегированы задачи, корректно интерпретирована и использована информация о команде.',
                'text_negative'       => 'Грубых ошибок при делегировании допущено не было',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.resource_quality'],
            [
                'text'                => 'Менеджер усвоил и корректно использовал информацию об уровне квалификации своих сотрудников, использовал контроль по результату для профессионального сотрудника и промежуточный контроль для слабого сотрудника.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Менеджер усвоил и корректно использовал информацию об уровне квалификации своих сотрудников, использовал контроль по результату для профессионального сотрудника и промежуточный контроль для слабого сотрудника.',
                'text_negative'       => 'Грубых ошибок при управлении сотрудниками допущено не было',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.feedback'],
            [
                'text'                => 'Менеджер давал сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Менеджер давал сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'text_negative'       => 'Грубых ошибок при использовании обратной связи допущено не было',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.1.+.
        $this->assertEquals(
            $results['management.communication_managment.comunication_channel'],
            [
                'text'                => 'Многократно были выбраны и  использованы каналы коммуникаций, наилучшим образом соответствующие специфике задачи, и/или экономные по времени.',
                'short_text'          => '(очень высокий уровень)',
                'pocket'               => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_mail'],
            [
                'text'                => 'Менеджер читал и отвечал на письма высокого и среднего приоритета.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Менеджер читал и отвечал на письма высокого и среднего приоритета.',
                'text_negative'       => 'Грубых ошибок при работе с почтой допущено не было',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.3.+.
        $this->assertEquals(
            $results['management.communication_managment.effective_calls'],
            [
                'text'                => 'Менеджер избирательно отвечал и совершал звонки, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'short_text'          => '(очень высокий уровень)',
                'pocket'               => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_meetings'],
            [
                'text'                => 'Менеджер избирательно соглашался на встречи, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Менеджер избирательно соглашался на встречи, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'text_negative'       => 'Грубых ошибок при управлении встречами допущено не было',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );
    }

    /**
     * Проверяем менеджерские навыки - они имеют комбинированные тексты "позитив+негатив"
     * 8. верхняя граница 4го кармана, позитив
     * Негатив по нулям
     */
    public function testTextForInfoGraphic_8() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        // 1.Менеджерские навыки. - 1.Всё по нулям.
        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"0",
                             "1_1":{
                                "+":"100",
                                "-":"0"
                             },
                             "1_2":{
                                "+":"100",
                                "-":"0"
                             },
                             "1_3":{
                                "+":"100",
                                "-":"0"
                             },
                             "1_4":{
                                "+":"0",
                                "-":"0"
                             }
                          },
                          "2":{
                             "total":"0",
                             "2_1":{
                                "+":"100",
                                "-":"0"
                             },
                             "2_2":{
                                "+":"100",
                                "-":"0"
                             },
                             "2_3":{
                                "+":"100",
                                "-":"0"
                             }
                          },
                          "3":{
                             "total":"0",
                             "3_1":{
                                "+":"100",
                                "-":"0"
                             },
                             "3_2":{
                                "+":"100",
                                "-":"0"
                             },
                             "3_3":{
                                "+":"100",
                                "-":"0"
                             },
                             "3_4":{
                                "+":"100",
                                "-":"0"
                             }
                          },
                          "total":"0"
                       },
                       "performance":{
                          "0":"0",
                          "1":"0",
                          "2":"0",
                          "total":"0",
                          "2_min":"0"
                       },
                       "time":{
                          "total":"0",
                          "workday_overhead_duration":"0",
                          "time_spend_for_1st_priority_activities":"0",
                          "time_spend_for_non_priority_activities":"0",
                          "time_spend_for_inactivity":"0",
                          "1st_priority_documents":"0",
                          "1st_priority_meetings":"0",
                          "1st_priority_phone_calls":"0",
                          "1st_priority_mail":"0",
                          "1st_priority_planning":"0",
                          "non_priority_documents":"0",
                          "non_priority_meetings":"0",
                          "non_priority_phone_calls":"0",
                          "non_priority_mail":"0",
                          "non_priority_planning":"0",
                          "efficiency":"0"
                       },
                       "overall":"0",
                       "percentile":{
                          "total":"0"
                       },
                       "personal":{
                          "9":"0",
                          "10":"0",
                          "11":"0",
                          "12":"0",
                          "13":"0",
                          "14":"0",
                          "15":"0",
                          "16":"0"
                       }
                    }
                '), true
            )
        );

        $results = SimulationResultTextService::generate($simulation, 'popup');

//        print_r($results);
//        die;

        $this->assertEquals(
            $results['management.task_managment.day_planing'],
            [
                'text'                => 'Менеджер запланировал свой рабочий день утром, было запланировано более 60% и менее 80% рабочего времени с временными интервалами между задачами.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Менеджер запланировал свой рабочий день утром, было запланировано более 60% и менее 80% рабочего времени с временными интервалами между задачами.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_planing'],
            [
                'text'                => 'В целом задачи поставлены в план с учетом их категорий по матрице важно/срочно.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'В целом задачи поставлены в план с учетом их категорий по матрице важно/срочно.',
                'text_negative'       => 'В работе по планированию не было грубых ошибок',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.task_managment.tasks_priority_execution'],
            [
                'text'                => 'При выполнении задач в ходе дня менеджер многократно верно интерпретирует категорию поступившей задачи и выполняет более приоритетные задачи перед менее приоритетными.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'При выполнении задач в ходе дня менеджер многократно верно интерпретирует категорию поступившей задачи и выполняет более приоритетные задачи перед менее приоритетными.',
                'text_negative'       => 'При выполнении задач в ходе дня было допущено менее пяти грубых ошибок: задача очень низкой категории была выполнена ранее задачи очень высокого приоритета.',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 1.4.-.
        $this->assertEquals(
            $results['management.task_managment.tasks_interruprion'],
            [
                'text'                => 'Менеджер редко прерывался (менее 8 раз) при выполнении задач высокой и средней категории, отвлекаясь на внешние раздражители и задачи низкой категории',
                'short_text'          => '(нет ошибок)',
                'pocket'               => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.delegation'],
            [
                'text'                => 'Были корректно определены и делегированы задачи, корректно интерпретирована и использована информация о команде.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Были корректно определены и делегированы задачи, корректно интерпретирована и использована информация о команде.',
                'text_negative'       => 'Грубых ошибок при делегировании допущено не было',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.resource_quality'],
            [
                'text'                => 'Менеджер усвоил и корректно использовал информацию об уровне квалификации своих сотрудников, использовал контроль по результату для профессионального сотрудника и промежуточный контроль для слабого сотрудника.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Менеджер усвоил и корректно использовал информацию об уровне квалификации своих сотрудников, использовал контроль по результату для профессионального сотрудника и промежуточный контроль для слабого сотрудника.',
                'text_negative'       => 'Грубых ошибок при управлении сотрудниками допущено не было',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        $this->assertEquals(
            $results['management.people_managment.feedback'],
            [
                'text'                => 'Менеджер давал сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Менеджер давал сотруднику позитивную и конструктивную обратную связь при выполнении задач и обсуждении результата.',
                'text_negative'       => 'Грубых ошибок при использовании обратной связи допущено не было',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.1.+.
        $this->assertEquals(
            $results['management.communication_managment.comunication_channel'],
            [
                'text'                => 'Многократно были выбраны и  использованы каналы коммуникаций, наилучшим образом соответствующие специфике задачи, и/или экономные по времени.',
                'short_text'          => '(очень высокий уровень)',
                'pocket'               => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_mail'],
            [
                'text'                => 'Менеджер читал и отвечал на письма высокого и среднего приоритета.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Менеджер читал и отвечал на письма высокого и среднего приоритета.',
                'text_negative'       => 'Грубых ошибок при работе с почтой допущено не было',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );

        // 3.3.+.
        $this->assertEquals(
            $results['management.communication_managment.effective_calls'],
            [
                'text'                => 'Менеджер избирательно отвечал и совершал звонки, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'short_text'          => '(очень высокий уровень)',
                'pocket'               => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment.effective_meetings'],
            [
                'text'                => 'Менеджер избирательно соглашался на встречи, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'short_text'          => '(очень высокий уровень)',
                'text_positive'       => 'Менеджер избирательно соглашался на встречи, учитывая личность и статус визави, а также категорию задач, которая может от него исходить.',
                'text_negative'       => 'Грубых ошибок при управлении встречами допущено не было',
                'short_text_positive' => 'очень высокий уровень',
                'short_text_negative' => 'нет ошибок'
            ]
        );
    }
} 