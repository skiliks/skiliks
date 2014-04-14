<?php

class SimulationResultTextServiceUnitTest extends CDbTestCase {

    public function testGenerate(){
        $this->markTestIncomplete();
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $etalon = [
            'management.task_managment.day_planing' =>
                [
                    'text'       => "Было запланировано менее 60% или более 100% рабочего времени или менеджер не приступал к планированию в начале рабочего дня. При этом менеджер не планировал свой рабочий день вообще.",
                    'short_text' => "(низкий уровень, есть ошибки)",
                ],
            'management.task_managment.tasks_priority_planing' =>
                [
                    'text' => "Не учтены категории задач по матрице важно/срочно при их постановке в план.",
                    'short_text' => "(низкий уровень)",
                ],
            'management.task_managment.tasks_priority_execution' =>
                [
                    'text' => "При выполнении задач в ходе дня менеджер многократно верно интерпретирует категорию поступившей задачи и выполняет более приоритетные задачи перед менее приоритетными.",
                    'short_text' => "(высокий уровень)",
                ],
            'management.task_managment.tasks_interruprion' =>
                [
                    'text' => "Менеджер редко прерывался (менее 8 раз) при выполнении задач высокой и средней категории, отвлекаясь на внешние раздражители и задачи низкой категории",
                    'short_text' => "(нет ошибок)",
                    'pocket' =>
                        [
                            'left' => "0",
                            'right' => "40",
                        ],
            ],
            'management.task_managment' =>
                [
                    'text' => "(средний уровень)",
                    'short_text' => "(средний уровень)",
                    'pocket' =>
                        [
                            'left' => "33",
                            'right' => "50",
                    ],
                ]
        ];

        $simulation->results_popup_cache = serialize(json_decode('{"management":{"1":{"total":"40.000000","1_1":{"+":"18.18","-":"100.00"},"1_2":{"+":"0.00","-":"0.00"},"1_3":{"+":"73.46","-":"0.00"},"1_4":{"+":"0.00","-":"0.00"}}}}', true));

        $simulation->save(false);

        $recommendations = SimulationResultTextService::generate($simulation, 'popup', true);

        $this->assertEquals($etalon, $recommendations);
    }

}
 