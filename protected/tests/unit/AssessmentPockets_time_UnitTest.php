<?php
/**
 * Группа тестов проверяет карманы для:
 * - Продуктивное время (выполнение приоритетных задач)
 * - Непродуктивное время (иные действия, не связанные с приоритетами)
 * - Время ожидания и бездействия
 * - Сверхурочное время
 * - Срочно
 * - Высокий приоритет
 * - Средний приоритет
 * - Двухминутные задачи
 * - 1. Управление задачами с учётом приоритетов
 * - 2. Управление людьми
 * - 3. Управление коммуникациями
 * - Управленческие навыки
 * - Результативность
 * - Эффективность использования времени
 * - Итоговый рейтинг
 *
 */

class AssessmentPockets_time_UnitTest extends CDbTestCase {

    use UnitTestBaseTrait;

    /**
     * 1. 1й карман - позитив, позитив по нулям
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
                                "-":"40"
                             },
                             "1_2":{
                                "+":"0",
                                "-":"40"
                             },
                             "1_3":{
                                "+":"0",
                                "-":"40"
                             },
                             "1_4":{
                                "+":"0",
                                "-":"40"
                             }
                          },
                          "2":{
                             "total":"0",
                             "2_1":{
                                "+":"0",
                                "-":"30"
                             },
                             "2_2":{
                                "+":"0",
                                "-":"30"
                             },
                             "2_3":{
                                "+":"0",
                                "-":"20"
                             }
                          },
                          "3":{
                             "total":"0",
                             "3_1":{
                                "+":"0",
                                "-":"40"
                             },
                             "3_2":{
                                "+":"0",
                                "-":"40"
                             },
                             "3_3":{
                                "+":"0",
                                "-":"40"
                             },
                             "3_4":{
                                "+":"0",
                                "-":"40"
                             }
                          },
                          "total":"0"
                       },
                       "performance":{
                          "0":     "0",
                          "1":     "0",
                          "2":     "0",
                          "total": "0",
                          "2_min": "0"
                       },
                       "time":{
                          "total":                                  "0",
                          "workday_overhead_duration":              "0",
                          "time_spend_for_1st_priority_activities": "0",
                          "time_spend_for_non_priority_activities": "0",
                          "time_spend_for_inactivity":              "0",
                          "1st_priority_documents":                 "0",
                          "1st_priority_meetings":                  "0",
                          "1st_priority_phone_calls":               "0",
                          "1st_priority_mail":                      "0",
                          "1st_priority_planning":                  "0",
                          "non_priority_documents":                 "0",
                          "non_priority_meetings":                  "0",
                          "non_priority_phone_calls":               "0",
                          "non_priority_mail":                      "0",
                          "non_priority_planning":                  "0",
                          "efficiency":                             "0"
                       },
                       "overall": "0",
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

//        $this->getArray($results, 'time.productive_time');
//        $this->getArray($results, 'time.waiting_time');
//        $this->getArray($results, 'time.over_time');
//
//        $this->getArray($results, 'performance.urgent');
//        $this->getArray($results, 'performance.high');
//        $this->getArray($results, 'performance.middle');
//        $this->getArray($results, 'performance.two_minutes');
//
//        $this->getArray($results, 'management.task_managment');
//        $this->getArray($results, 'management.people_managment');
//        $this->getArray($results, 'management.communication_managment');
//        $this->getArray($results, 'management');
//        $this->getArray($results, 'performance');
//        $this->getArray($results, 'time');
//        $this->getArray($results, 'overall');

        $this->assertEquals(
            $results['time.productive_time'],
            [
                'text'       => 'Недостаточно времени симуляции (менее 70%) было выделено на выполнение приоритетных задач, обеспечивающих достижение результата',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 55,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.waiting_time'],
            [
                'text'       => 'Менее 15% времени симуляции менеджер бездействовал (знакомился с интерфейсом и обдумывал последующие шаги)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 7.5,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.over_time'],
            [
                'text'       => 'Не было использовано сверхурочное время (симуляция завершена в 18.00) или было использовано до 30 игровых минут сверхурочного времени для завершения приоритетных задач',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 15,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.urgent'],
            [
                'text'       => 'Не была определена и выполнена задача самого высокого приоритета (по матрице срочно/важно)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.high'],
            [
                'text'       => 'Не была определена и выполнена задача первого приоритета (по матрице срочно/важно)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.middle'],
            [
                'text'       => 'Не была определена и выполнена задача второго приоритета (по матрице срочно/важно)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.two_minutes'],
            [
                'text'       => 'Было идентифицировано и выполнено менее 60% двухминутных задач (в основном - из почты)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.task_managment'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 33,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 33,
                ]
            ]
        );

        $this->assertEquals(
            $results['management'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 33,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['time'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 55,
                ]
            ]
        );

        $this->assertEquals(
            $results['overall'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 33,
                ]
            ]
        );
    }

    /**
     * 2. верхняя граница 1го кармана, позитив
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
                             "total":"32",
                             "1_1":{
                                "+":"0",
                                "-":"40"
                             },
                             "1_2":{
                                "+":"0",
                                "-":"40"
                             },
                             "1_3":{
                                "+":"0",
                                "-":"40"
                             },
                             "1_4":{
                                "+":"0",
                                "-":"40"
                             }
                          },
                          "2":{
                             "total":"39",
                             "2_1":{
                                "+":"0",
                                "-":"30"
                             },
                             "2_2":{
                                "+":"0",
                                "-":"30"
                             },
                             "2_3":{
                                "+":"0",
                                "-":"20"
                             }
                          },
                          "3":{
                             "total":"32",
                             "3_1":{
                                "+":"0",
                                "-":"40"
                             },
                             "3_2":{
                                "+":"0",
                                "-":"40"
                             },
                             "3_3":{
                                "+":"0",
                                "-":"40"
                             },
                             "3_4":{
                                "+":"0",
                                "-":"40"
                             }
                          },
                          "total":"32"
                       },
                       "performance":{
                          "0":     "39",
                          "1":     "39",
                          "2":     "39",
                          "2_min": "39",
                          "total": "39"
                       },
                       "time":{
                          "total":                                  "54",
                          "workday_overhead_duration":              "14",
                          "time_spend_for_1st_priority_activities": "54",
                          "time_spend_for_non_priority_activities": "0",
                          "time_spend_for_inactivity":              "7,49",
                          "1st_priority_documents":                 "0",
                          "1st_priority_meetings":                  "0",
                          "1st_priority_phone_calls":               "0",
                          "1st_priority_mail":                      "0",
                          "1st_priority_planning":                  "0",
                          "non_priority_documents":                 "0",
                          "non_priority_meetings":                  "0",
                          "non_priority_phone_calls":               "0",
                          "non_priority_mail":                      "0",
                          "non_priority_planning":                  "0",
                          "efficiency":                             "0"
                       },
                       "overall": "32",
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

//        $this->getArray($results, 'time.productive_time');
//        $this->getArray($results, 'time.waiting_time');
//        $this->getArray($results, 'time.over_time');
//
//        $this->getArray($results, 'performance.urgent');
//        $this->getArray($results, 'performance.high');
//        $this->getArray($results, 'performance.middle');
//        $this->getArray($results, 'performance.two_minutes');
//
//        $this->getArray($results, 'management.task_managment');
//        $this->getArray($results, 'management.people_managment');
//        $this->getArray($results, 'management.communication_managment');
//        $this->getArray($results, 'management');
//        $this->getArray($results, 'performance');
//        $this->getArray($results, 'time');
//        $this->getArray($results, 'overall');
//
//      die();

        $this->assertEquals(
            $results['time.productive_time'],
            [
                'text'       => 'Недостаточно времени симуляции (менее 70%) было выделено на выполнение приоритетных задач, обеспечивающих достижение результата',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 55,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.waiting_time'],
            [
                'text'       => 'Менее 15% времени симуляции менеджер бездействовал (знакомился с интерфейсом и обдумывал последующие шаги)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 7.5,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.over_time'],
            [
                'text'       => 'Не было использовано сверхурочное время (симуляция завершена в 18.00) или было использовано до 30 игровых минут сверхурочного времени для завершения приоритетных задач',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 15,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.urgent'],
            [
                'text'       => 'Не была определена и выполнена задача самого высокого приоритета (по матрице срочно/важно)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.high'],
            [
                'text'       => 'Не была определена и выполнена задача первого приоритета (по матрице срочно/важно)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.middle'],
            [
                'text'       => 'Не была определена и выполнена задача второго приоритета (по матрице срочно/важно)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.two_minutes'],
            [
                'text'       => 'Было идентифицировано и выполнено менее 60% двухминутных задач (в основном - из почты)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.task_managment'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 33,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 33,
                ]
            ]
        );

        $this->assertEquals(
            $results['management'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 33,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 40,
                ]
            ]
        );

        $this->assertEquals(
            $results['time'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 55,
                ]
            ]
        );

        $this->assertEquals(
            $results['overall'],
            [
                'text'       => '(низкий уровень)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 0,
                    'right' => 33,
                ]
            ]
        );
    }

    /**
     * 3. нижняя граница 2го кармана, позитив
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
                             "total":"33",
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
                             "total":"40",
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
                             "total":"33",
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
                          "total":"33"
                       },
                       "performance":{
                          "0":     "40",
                          "1":     "40",
                          "2":     "40",
                          "2_min": "40",
                          "total": "40"
                       },
                       "time":{
                          "total":                                  "55",
                          "workday_overhead_duration":              "15",
                          "time_spend_for_1st_priority_activities": "55",
                          "time_spend_for_non_priority_activities": "0",
                          "time_spend_for_inactivity":              "7.5",
                          "1st_priority_documents":                 "0",
                          "1st_priority_meetings":                  "0",
                          "1st_priority_phone_calls":               "0",
                          "1st_priority_mail":                      "0",
                          "1st_priority_planning":                  "0",
                          "non_priority_documents":                 "0",
                          "non_priority_meetings":                  "0",
                          "non_priority_phone_calls":               "0",
                          "non_priority_mail":                      "0",
                          "non_priority_planning":                  "0",
                          "efficiency":                             "0"
                       },
                       "overall": "33",
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

//        $this->getArray($results, 'time.productive_time');
//        $this->getArray($results, 'time.waiting_time');
//        $this->getArray($results, 'time.over_time');
//
//        $this->getArray($results, 'performance.urgent');
//        $this->getArray($results, 'performance.high');
//        $this->getArray($results, 'performance.middle');
//        $this->getArray($results, 'performance.two_minutes');
//
//        $this->getArray($results, 'management.task_managment');
//        $this->getArray($results, 'management.people_managment');
//        $this->getArray($results, 'management.communication_managment');
//        $this->getArray($results, 'management');
//        $this->getArray($results, 'performance');
//        $this->getArray($results, 'time');
//        $this->getArray($results, 'overall');
//
//        die();

        $this->assertEquals(
            $results['time.productive_time'],
            [
                'text'       => 'Недостаточно времени симуляции (менее 70%) было выделено на выполнение приоритетных задач, обеспечивающих достижение результата',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 55,
                    'right' => 70,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.waiting_time'],
            [
                'text'       => 'Менее 15% времени симуляции менеджер бездействовал (знакомился с интерфейсом и обдумывал последующие шаги)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 7.5,
                    'right' => 15,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.over_time'],
            [
                'text'       => 'Не было использовано сверхурочное время (симуляция завершена в 18.00) или было использовано до 30 игровых минут сверхурочного времени для завершения приоритетных задач',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 15,
                    'right' => 30,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.urgent'],
            [
                'text'       => 'Не была определена и выполнена задача самого высокого приоритета (по матрице срочно/важно)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.high'],
            [
                'text'       => 'Не была определена и выполнена задача первого приоритета (по матрице срочно/важно)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.middle'],
            [
                'text'       => 'Не была определена и выполнена задача второго приоритета (по матрице срочно/важно)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.two_minutes'],
            [
                'text'       => 'Было идентифицировано и выполнено менее 60% двухминутных задач (в основном - из почты)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.task_managment'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 33,
                    'right' => 50,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 33,
                    'right' => 50,
                ]
            ]
        );

        $this->assertEquals(
            $results['management'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 33,
                    'right' => 50,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['time'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 55,
                    'right' => 70,
                ]
            ]
        );

        $this->assertEquals(
            $results['overall'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 33,
                    'right' => 50,
                ]
            ]
        );
    }

    /**
     * 4. верхняя граница 2го кармана, позитив
     */
    public function testTextForInfoGraphic_4() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        
        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"49",
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
                             "total":"59",
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
                             "total":"49",
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
                          "total":"49"
                       },
                       "performance":{
                          "0":     "59",
                          "1":     "59",
                          "2":     "59",
                          "2_min": "59",
                          "total": "59"
                       },
                       "time":{
                          "total":                                  "69",
                          "workday_overhead_duration":              "29",
                          "time_spend_for_1st_priority_activities": "69",
                          "time_spend_for_non_priority_activities": "0",
                          "time_spend_for_inactivity":              "14",
                          "1st_priority_documents":                 "0",
                          "1st_priority_meetings":                  "0",
                          "1st_priority_phone_calls":               "0",
                          "1st_priority_mail":                      "0",
                          "1st_priority_planning":                  "0",
                          "non_priority_documents":                 "0",
                          "non_priority_meetings":                  "0",
                          "non_priority_phone_calls":               "0",
                          "non_priority_mail":                      "0",
                          "non_priority_planning":                  "0",
                          "efficiency":                             "0"
                       },
                       "overall": "74",
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

//        $this->getArray($results, 'time.productive_time');
//        $this->getArray($results, 'time.waiting_time');
//        $this->getArray($results, 'time.over_time');
//
//        $this->getArray($results, 'performance.urgent');
//        $this->getArray($results, 'performance.high');
//        $this->getArray($results, 'performance.middle');
//        $this->getArray($results, 'performance.two_minutes');
//
//        $this->getArray($results, 'management.task_managment');
//        $this->getArray($results, 'management.people_managment');
//        $this->getArray($results, 'management.communication_managment');
//        $this->getArray($results, 'management');
//        $this->getArray($results, 'performance');
//        $this->getArray($results, 'time');
//        $this->getArray($results, 'overall');
//
//        die();

        $this->assertEquals(
            $results['time.productive_time'],
            [
                'text'       => 'Недостаточно времени симуляции (менее 70%) было выделено на выполнение приоритетных задач, обеспечивающих достижение результата',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 55,
                    'right' => 70,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.waiting_time'],
            [
                'text'       => 'Менее 15% времени симуляции менеджер бездействовал (знакомился с интерфейсом и обдумывал последующие шаги)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 7.5,
                    'right' => 15,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.over_time'],
            [
                'text'       => 'Не было использовано сверхурочное время (симуляция завершена в 18.00) или было использовано до 30 игровых минут сверхурочного времени для завершения приоритетных задач',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 15,
                    'right' => 30,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.urgent'],
            [
                'text'       => 'Не была определена и выполнена задача самого высокого приоритета (по матрице срочно/важно)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.high'],
            [
                'text'       => 'Не была определена и выполнена задача первого приоритета (по матрице срочно/важно)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.middle'],
            [
                'text'       => 'Не была определена и выполнена задача второго приоритета (по матрице срочно/важно)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.two_minutes'],
            [
                'text'       => 'Было идентифицировано и выполнено менее 60% двухминутных задач (в основном - из почты)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.task_managment'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 33,
                    'right' => 50,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 33,
                    'right' => 50,
                ]
            ]
        );

        $this->assertEquals(
            $results['management'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 33,
                    'right' => 50,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 40,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['time'],
            [
                'text'       => '(средний уровень)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 55,
                    'right' => 70,
                ]
            ]
        );

        $this->assertEquals(
            $results['overall'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 50,
                    'right' => 75,
                ]
            ]
        );
    }

    /**
     * 5. нижняя граница 3го кармана, позитив
     */
    public function testTextForInfoGraphic_5() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"50",
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
                             "total":"60",
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
                             "total":"50",
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
                          "total":"50"
                       },
                       "performance":{
                          "0":     "60",
                          "1":     "60",
                          "2":     "60",
                          "2_min": "60",
                          "total": "60"
                       },
                       "time":{
                          "total":                                  "70",
                          "workday_overhead_duration":              "30",
                          "time_spend_for_1st_priority_activities": "70",
                          "time_spend_for_non_priority_activities": "0",
                          "time_spend_for_inactivity":              "15",
                          "1st_priority_documents":                 "0",
                          "1st_priority_meetings":                  "0",
                          "1st_priority_phone_calls":               "0",
                          "1st_priority_mail":                      "0",
                          "1st_priority_planning":                  "0",
                          "non_priority_documents":                 "0",
                          "non_priority_meetings":                  "0",
                          "non_priority_phone_calls":               "0",
                          "non_priority_mail":                      "0",
                          "non_priority_planning":                  "0",
                          "efficiency":                             "0"
                       },
                       "overall": "50",
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

//        $this->getArray($results, 'time.productive_time');
//        $this->getArray($results, 'time.waiting_time');
//        $this->getArray($results, 'time.over_time');
//
//        $this->getArray($results, 'performance.urgent');
//        $this->getArray($results, 'performance.high');
//        $this->getArray($results, 'performance.middle');
//        $this->getArray($results, 'performance.two_minutes');
//
//        $this->getArray($results, 'management.task_managment');
//        $this->getArray($results, 'management.people_managment');
//        $this->getArray($results, 'management.communication_managment');
//        $this->getArray($results, 'management');
//        $this->getArray($results, 'performance');
//        $this->getArray($results, 'time');
//        $this->getArray($results, 'overall');
//
//        die();

        $this->assertEquals(
            $results['time.productive_time'],
            [
                'text'       => 'Значительная часть времени симуляции (от 70% и выше) была посвящена выполнению приоритетных задач, обеспечивающих достижение результата',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 70,
                    'right' => 85,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.waiting_time'],
            [
                'text'       => 'Более 15% времени симуляции менеджер бездействовал (знакомился с интерфейсом и обдумывал последующие шаги)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 15,
                    'right' => 30,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.over_time'],
            [
                'text'       => 'Было использовано сверхурочное время (более 30 игровых минут)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 30,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.urgent'],
            [
                'text'       => 'Как минимум, была определена и выполнена в значительной степени задача самого высокого приоритета (по матрице срочно/важно)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.high'],
            [
                'text'       => 'Как минимум, была определена и выполнена в значительной степени задача первого приоритета (по матрице срочно/важно)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.middle'],
            [
                'text'       => 'Как минимум, была определена и выполнена задача второго приоритета (по матрице срочно/важно)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.two_minutes'],
            [
                'text'       => 'Было идентифицировано и выполнено более 60% двухминутных задач (в основном - из почты)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.task_managment'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 50,
                    'right' => 75,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 50,
                    'right' => 75,
                ]
            ]
        );

        $this->assertEquals(
            $results['management'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 50,
                    'right' => 75,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['time'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 70,
                    'right' => 85,
                ]
            ]
        );

        $this->assertEquals(
            $results['overall'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 50,
                    'right' => 75,
                ]
            ]
        );
    }

    /**
     * 6. верхняя граница 3го кармана, позитив
     */
    public function testTextForInfoGraphic_6() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"74",
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
                             "total":"79",
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
                             "total":"74",
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
                          "total":"74"
                       },
                       "performance":{
                          "0":     "79",
                          "1":     "79",
                          "2":     "79",
                          "2_min": "79",
                          "total": "79"
                       },
                       "time":{
                          "total":                                  "84",
                          "workday_overhead_duration":              "59",
                          "time_spend_for_1st_priority_activities": "84",
                          "time_spend_for_non_priority_activities": "0",
                          "time_spend_for_inactivity":              "29",
                          "1st_priority_documents":                 "0",
                          "1st_priority_meetings":                  "0",
                          "1st_priority_phone_calls":               "0",
                          "1st_priority_mail":                      "0",
                          "1st_priority_planning":                  "0",
                          "non_priority_documents":                 "0",
                          "non_priority_meetings":                  "0",
                          "non_priority_phone_calls":               "0",
                          "non_priority_mail":                      "0",
                          "non_priority_planning":                  "0",
                          "efficiency":                             "0"
                       },
                       "overall": "74",
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

//        $this->getArray($results, 'time.productive_time');
//        $this->getArray($results, 'time.waiting_time');
//        $this->getArray($results, 'time.over_time');
//
//        $this->getArray($results, 'performance.urgent');
//        $this->getArray($results, 'performance.high');
//        $this->getArray($results, 'performance.middle');
//        $this->getArray($results, 'performance.two_minutes');
//
//        $this->getArray($results, 'management.task_managment');
//        $this->getArray($results, 'management.people_managment');
//        $this->getArray($results, 'management.communication_managment');
//        $this->getArray($results, 'management');
//        $this->getArray($results, 'performance');
//        $this->getArray($results, 'time');
//        $this->getArray($results, 'overall');
//
//        die();

        $this->assertEquals(
            $results['time.productive_time'],
            [
                'text'       => 'Значительная часть времени симуляции (от 70% и выше) была посвящена выполнению приоритетных задач, обеспечивающих достижение результата',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 70,
                    'right' => 85,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.waiting_time'],
            [
                'text'       => 'Более 15% времени симуляции менеджер бездействовал (знакомился с интерфейсом и обдумывал последующие шаги)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 15,
                    'right' => 30,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.over_time'],
            [
                'text'       => 'Было использовано сверхурочное время (более 30 игровых минут)',
                'short_text' => '(средний уровень)',
                'pocket'     => [
                    'left'  => 30,
                    'right' => 60,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.urgent'],
            [
                'text'       => 'Как минимум, была определена и выполнена в значительной степени задача самого высокого приоритета (по матрице срочно/важно)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.high'],
            [
                'text'       => 'Как минимум, была определена и выполнена в значительной степени задача первого приоритета (по матрице срочно/важно)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.middle'],
            [
                'text'       => 'Как минимум, была определена и выполнена задача второго приоритета (по матрице срочно/важно)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.two_minutes'],
            [
                'text'       => 'Было идентифицировано и выполнено более 60% двухминутных задач (в основном - из почты)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.task_managment'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 50,
                    'right' => 75,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 50,
                    'right' => 75,
                ]
            ]
        );

        $this->assertEquals(
            $results['management'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 50,
                    'right' => 75,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 80,
                ]
            ]
        );

        $this->assertEquals(
            $results['time'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 70,
                    'right' => 85,
                ]
            ]
        );

        $this->assertEquals(
            $results['overall'],
            [
                'text'       => '(высокий уровень)',
                'short_text' => '(высокий уровень)',
                'pocket'     => [
                    'left'  => 50,
                    'right' => 75,
                ]
            ]
        );
    }

    /**
     * 7. нижняя граница 4го кармана, позитив
     */
    public function testTextForInfoGraphic_7() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"75",
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
                             "total":"80",
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
                             "total":"75",
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
                          "total":"75"
                       },
                       "performance":{
                          "0":     "80",
                          "1":     "80",
                          "2":     "80",
                          "2_min": "80",
                          "total": "80"
                       },
                       "time":{
                          "total":                                  "85",
                          "workday_overhead_duration":              "60",
                          "time_spend_for_1st_priority_activities": "85",
                          "time_spend_for_non_priority_activities": "0",
                          "time_spend_for_inactivity":              "30",
                          "1st_priority_documents":                 "0",
                          "1st_priority_meetings":                  "0",
                          "1st_priority_phone_calls":               "0",
                          "1st_priority_mail":                      "0",
                          "1st_priority_planning":                  "0",
                          "non_priority_documents":                 "0",
                          "non_priority_meetings":                  "0",
                          "non_priority_phone_calls":               "0",
                          "non_priority_mail":                      "0",
                          "non_priority_planning":                  "0",
                          "efficiency":                             "0"
                       },
                       "overall": "75",
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

//        $this->getArray($results, 'time.productive_time');
//        $this->getArray($results, 'time.waiting_time');
//        $this->getArray($results, 'time.over_time');
//
//        $this->getArray($results, 'performance.urgent');
//        $this->getArray($results, 'performance.high');
//        $this->getArray($results, 'performance.middle');
//        $this->getArray($results, 'performance.two_minutes');
//
//        $this->getArray($results, 'management.task_managment');
//        $this->getArray($results, 'management.people_managment');
//        $this->getArray($results, 'management.communication_managment');
//        $this->getArray($results, 'management');
//        $this->getArray($results, 'performance');
//        $this->getArray($results, 'time');
//        $this->getArray($results, 'overall');
//
//        die();

        $this->assertEquals(
            $results['time.productive_time'],
            [
                'text'       => 'Значительная часть времени симуляции (от 70% и выше) была посвящена выполнению приоритетных задач, обеспечивающих достижение результата',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 85,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.waiting_time'],
            [
                'text'       => 'Более 15% времени симуляции менеджер бездействовал (знакомился с интерфейсом и обдумывал последующие шаги)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 30,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.over_time'],
            [
                'text'       => 'Было использовано сверхурочное время (более 30 игровых минут)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 120,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.urgent'],
            [
                'text'       => 'Как минимум, была определена и выполнена в значительной степени задача самого высокого приоритета (по матрице срочно/важно)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.high'],
            [
                'text'       => 'Как минимум, была определена и выполнена в значительной степени задача первого приоритета (по матрице срочно/важно)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.middle'],
            [
                'text'       => 'Как минимум, была определена и выполнена задача второго приоритета (по матрице срочно/важно)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.two_minutes'],
            [
                'text'       => 'Было идентифицировано и выполнено более 60% двухминутных задач (в основном - из почты)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.task_managment'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 75,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 75,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 75,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['time'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 85,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['overall'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 75,
                    'right' => 100,
                ]
            ]
        );
    }

    /**
     * 8. верхняя граница 4го кармана, позитив
     */
    public function testTextForInfoGraphic_8() {

        $scenarioFull = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $simulation = new Simulation();
        $simulation->assessment_version === Simulation::ASSESSMENT_VERSION_2;
        $simulation->game_type = $scenarioFull;

        $simulation->results_popup_cache = serialize(
            json_decode(
                str_replace([' ', "\n"], '' ,'
                    {
                       "management":{
                          "1":{
                             "total":"100",
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
                             "total":"100",
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
                             "total":"100",
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
                          "total":"100"
                       },
                       "performance":{
                          "0":     "100",
                          "1":     "100",
                          "2":     "100",
                          "2_min": "100",
                          "total": "100"
                       },
                       "time":{
                          "total":                                  "100",
                          "workday_overhead_duration":              "1000",
                          "time_spend_for_1st_priority_activities": "100",
                          "time_spend_for_non_priority_activities": "100",
                          "time_spend_for_inactivity":              "100",
                          "1st_priority_documents":                 "0",
                          "1st_priority_meetings":                  "0",
                          "1st_priority_phone_calls":               "0",
                          "1st_priority_mail":                      "0",
                          "1st_priority_planning":                  "0",
                          "non_priority_documents":                 "0",
                          "non_priority_meetings":                  "0",
                          "non_priority_phone_calls":               "0",
                          "non_priority_mail":                      "0",
                          "non_priority_planning":                  "0",
                          "efficiency":                             "0"
                       },
                       "overall": "100",
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

//        $this->getArray($results, 'time.productive_time');
//        $this->getArray($results, 'time.waiting_time');
//        $this->getArray($results, 'time.over_time');
//
//        $this->getArray($results, 'performance.urgent');
//        $this->getArray($results, 'performance.high');
//        $this->getArray($results, 'performance.middle');
//        $this->getArray($results, 'performance.two_minutes');
//
//        $this->getArray($results, 'management.task_managment');
//        $this->getArray($results, 'management.people_managment');
//        $this->getArray($results, 'management.communication_managment');
//        $this->getArray($results, 'management');
//        $this->getArray($results, 'performance');
//        $this->getArray($results, 'time');
//        $this->getArray($results, 'overall');
//
//        die();

        $this->assertEquals(
            $results['time.productive_time'],
            [
                'text'       => 'Значительная часть времени симуляции (от 70% и выше) была посвящена выполнению приоритетных задач, обеспечивающих достижение результата',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 85,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.waiting_time'],
            [
                'text'       => 'Более 15% времени симуляции менеджер бездействовал (знакомился с интерфейсом и обдумывал последующие шаги)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 30,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['time.over_time'],
            [
                'text'       => 'Было использовано сверхурочное время (более 30 игровых минут)',
                'short_text' => '(низкий уровень)',
                'pocket'     => [
                    'left'  => 60,
                    'right' => 120,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.urgent'],
            [
                'text'       => 'Как минимум, была определена и выполнена в значительной степени задача самого высокого приоритета (по матрице срочно/важно)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.high'],
            [
                'text'       => 'Как минимум, была определена и выполнена в значительной степени задача первого приоритета (по матрице срочно/важно)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.middle'],
            [
                'text'       => 'Как минимум, была определена и выполнена задача второго приоритета (по матрице срочно/важно)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance.two_minutes'],
            [
                'text'       => 'Было идентифицировано и выполнено более 60% двухминутных задач (в основном - из почты)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.task_managment'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 75,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.people_managment'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management.communication_managment'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 75,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['management'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 75,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['performance'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 80,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['time'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 85,
                    'right' => 100,
                ]
            ]
        );

        $this->assertEquals(
            $results['overall'],
            [
                'text'       => '(очень высокий уровень)',
                'short_text' => '(очень высокий уровень)',
                'pocket'     => [
                    'left'  => 75,
                    'right' => 100,
                ]
            ]
        );
    }

    /**
     * @param array $results, возвращается из SimulationResultTextService::generate()
     * @param string $label
     */
    public function getArray($results, $label) {
        echo sprintf(
            '       $this->assertEquals(
                $results[\'%s\'],
                [
                    \'text\'       => \'%s\',
                    \'short_text\' => \'%s\',
                    \'pocket\'     => [
                        \'left\'  => %s,
                        \'right\' => %s,
                    ]
                ]
            );'."\n"."\n",
            $label,
            $results[$label]['text'],
            $results[$label]['short_text'],
            $results[$label]['pocket']['left'],
            $results[$label]['pocket']['right']
        );
    }
} 