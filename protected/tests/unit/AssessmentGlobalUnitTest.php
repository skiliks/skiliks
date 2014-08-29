<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 4/26/13
 * Time: 11:40 AM
 * To change this template use File | Settings | File Templates.
 */
class AssessmentGlobalUnitTest extends CDbTestCase
{
    use UnitLoggingTrait;
    use UnitTestBaseTrait;

    /**
     * 100% efficiency of managerial skills test
     */

    public function testAssessment_Goals_Areas_Overals_case1()
    {
        // $this->markTestSkipped(); // wait for new assessment scheme

        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // ---
        $this->addAssessmentAggregated($simulation, '214d0');
        $this->addAssessmentAggregated($simulation, '214d1');
        $this->addAssessmentAggregated($simulation, '214d2');
        $this->addAssessmentAggregated($simulation, '214d3');
        $this->addAssessmentAggregated($simulation, '214d4');

        $this->addAssessmentAggregated($simulation, '341a8'); // 3)
        $this->addAssessmentAggregated($simulation, '341b1'); // 3)
        $this->addAssessmentAggregated($simulation, '341b5'); // 3)
        $this->addAssessmentAggregated($simulation, '341b7'); // 3)

        $this->addAssessmentAggregated($simulation, '4121'); // 8
        $this->addAssessmentAggregated($simulation, '4124'); // 8

        $this->addAssessmentAggregated($simulation, '4122'); // 8
        $this->addAssessmentAggregated($simulation, '4125'); // 8
        $this->addAssessmentAggregated($simulation, '4141'); // 8
        $this->addAssessmentAggregated($simulation, '4143'); // 8
        $this->addAssessmentAggregated($simulation, '4153'); // 8
        $this->addAssessmentAggregated($simulation, '4127'); // 8

        $this->addAssessmentAggregated($simulation, '3214'); // 3)
        $this->addAssessmentAggregated($simulation, '3216'); // 3)
        $this->addAssessmentAggregated($simulation, '3218'); // 3)

        //$this->addAssessmentAggregated($simulation, '1122');
        //$this->addAssessmentAggregated($simulation, '1232'); // 1

        $this->addAssessmentAggregated($simulation, '351a1');
        $this->addAssessmentAggregated($simulation, '351b1');
        $this->addAssessmentAggregated($simulation, '351b2');
        $this->addAssessmentAggregated($simulation, '351b3');
        $this->addAssessmentAggregated($simulation, '351c1');
        $this->addAssessmentAggregated($simulation, '351c2');
        $this->addAssessmentAggregated($simulation, '351c3');

        $this->addAssessmentAggregated($simulation, '3311');
        $this->addAssessmentAggregated($simulation, '3312');
        $this->addAssessmentAggregated($simulation, '3313');
        $this->addAssessmentAggregated($simulation, '3322');
        $this->addAssessmentAggregated($simulation, '3323');
        $this->addAssessmentAggregated($simulation, '3326');
        $this->addAssessmentAggregated($simulation, '3332');
        $this->addAssessmentAggregated($simulation, '3333');

        $this->addAssessmentAggregated($simulation, '214a1');
        $this->addAssessmentAggregated($simulation, '214a3');
        $this->addAssessmentAggregated($simulation, '214a4');
        $this->addAssessmentAggregated($simulation, '214a5');
        $this->addAssessmentAggregated($simulation, '214b0');
        $this->addAssessmentAggregated($simulation, '214b1');
        $this->addAssessmentAggregated($simulation, '214b2');
        $this->addAssessmentAggregated($simulation, '214b3');
        //$this->addAssessmentAggregated($simulation, '214b4');
        $this->addAssessmentAggregated($simulation, '214b9');

        $this->addAssessmentAggregated($simulation, '8311', 100);
        $this->addAssessmentAggregated($simulation, '8351', 100);
        $this->addAssessmentAggregated($simulation, '8331', 100);
        $this->addAssessmentAggregated($simulation, '8381', 100);
        $this->addAssessmentAggregated($simulation, '8211', 100); // Выполняет свои обещания (% выполненных обещаний)
        $this->addAssessmentAggregated($simulation, '8212', 100); // Несёт ответственность за свои поступки
        $this->addAssessmentAggregated($simulation, '8213', 100); // Несёт ответственность за своих подчинённых
        $this->addAssessmentAggregated($simulation, '8341', 100);
        $this->addAssessmentAggregated($simulation, '8371', 100);
        $this->addAssessmentAggregated($simulation, '7211', 100);
        $this->addAssessmentAggregated($simulation, '7141', 150); // Stress resistance
        $this->addAssessmentAggregated($simulation, '8391', 150); // Гибкость
        $this->addAssessmentAggregated($simulation, '8111', 150); // Внимательность

        // -------------------------------

        $this->addPerformanceAggregated($simulation, '0'    , 100, 10);
        $this->addPerformanceAggregated($simulation, '1'    , 100, 10);
        $this->addPerformanceAggregated($simulation, '2'    , 100, 10);
        $this->addPerformanceAggregated($simulation, '2_min', 100, 10);

        // ---------------------------

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS, 282);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL, 80);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS, 40);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS, 80);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING, 0);

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS, 0);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL, 0);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS, 0);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS, 0);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING, 0);

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES, 97);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES, 0);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_INACTIVITY, 3);

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_WORKDAY_OVERHEAD_DURATION, 12);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_EFFICIENCY, 100);

        $learningGoalAnalyzer = new LearningGoalAnalyzer($simulation);
        $learningGoalAnalyzer->run();

        $learning_area = new LearningAreaAnalyzer($simulation);
        $learning_area->run();

        $evaluation = new Evaluation($simulation);
        $evaluation->run();

        $areas   = SimulationLearningArea::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $overall = AssessmentOverall::model()->findAllByAttributes(['sim_id' => $simulation->id]);

        $v = [
            'Управление задачами с учётом приоритетов' => 100,
            'Управление людьми'         => 100,
            'Управление коммуникациями' => 100,
            // personal (№8):
            'Устойчивость к манипуляциям и давлению' => 100,
            'Стрессоустойчивость'       => 100,
            'Ответственность'           => 100,
            'Принятие решения'          => 100,
            'Ориентация на результат'   => 100,
            'Конструктивность'          => 100,
            'Гибкость'                  => 100,
            'Внимательность'            => 100,
        ];

        foreach ($areas as $listItem) {
//            echo sprintf(
//                "%s %s \n",
//                $listItem->learningArea->title,
//                $listItem->value
//            );
            $this->assertEquals(
                $v[$listItem->learningArea->title],
                $listItem->value,
                'Areas: '.$listItem->learningArea->title
            );
        }

        $v = [
            'management'  => 100,
            'overall'     => 100,
            'performance' => 100,
            'time'        => 100,
        ];

        foreach ($overall as $listItem) {
//            echo sprintf(
//                "%s %s \n",
//                $listItem->assessment_category_code,
//                $listItem->value
//            );
            $this->assertEquals(
                $v[$listItem->assessment_category_code],
                round($listItem->value),
                'Overals: '.$listItem->assessment_category_code
            );
        }
    }

    public function testAssessment_Goals_Areas_Overals_case2()
    {
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // ---
        $this->addAssessmentAggregated($simulation, '3312');
        $this->addAssessmentAggregated($simulation, '341a8');

        $this->addAssessmentAggregated($simulation, '214d0'); // 1)
        $this->addAssessmentAggregated($simulation, '214d1'); // 1)
        $this->addAssessmentAggregated($simulation, '214d2'); // 1)
        $this->addAssessmentAggregated($simulation, '214d3'); // 1)
        $this->addAssessmentAggregated($simulation, '214d4'); // 1)

        $this->addAssessmentAggregated($simulation, '341b5');
        $this->addAssessmentAggregated($simulation, '341b7');
        $this->addAssessmentAggregated($simulation, '4121'); // 8
        $this->addAssessmentAggregated($simulation, '4124'); // 8
        $this->addAssessmentAggregated($simulation, '3216'); // 3)
        $this->addAssessmentAggregated($simulation, '4122'); // 8
        $this->addAssessmentAggregated($simulation, '341b1');
        $this->addAssessmentAggregated($simulation, '351b3');
        $this->addAssessmentAggregated($simulation, '4125'); // 8
        $this->addAssessmentAggregated($simulation, '4141'); // 8
        $this->addAssessmentAggregated($simulation, '4143'); // 8
        $this->addAssessmentAggregated($simulation, '4153'); // 8
        $this->addAssessmentAggregated($simulation, '4127'); // 8
        $this->addAssessmentAggregated($simulation, '3214'); // 3)

        $this->addAssessmentAggregated($simulation, '1232'); // 1)
        $this->addAssessmentAggregated($simulation, '1122'); // 1

        $this->addAssessmentAggregated($simulation, '3218'); // 1
        $this->addAssessmentAggregated($simulation, '351b2');
        $this->addAssessmentAggregated($simulation, '351b1');
        $this->addAssessmentAggregated($simulation, '351c1');
        $this->addAssessmentAggregated($simulation, '351c2');
        $this->addAssessmentAggregated($simulation, '351a1');
        $this->addAssessmentAggregated($simulation, '351a4');
        $this->addAssessmentAggregated($simulation, '351b4');
        $this->addAssessmentAggregated($simulation, '351c3');
        $this->addAssessmentAggregated($simulation, '3322');
        $this->addAssessmentAggregated($simulation, '3323');
        $this->addAssessmentAggregated($simulation, '3313');
        $this->addAssessmentAggregated($simulation, '3333');
        $this->addAssessmentAggregated($simulation, '3326');
        $this->addAssessmentAggregated($simulation, '3311');
        $this->addAssessmentAggregated($simulation, '3332');

        $this->addAssessmentAggregated($simulation, '214a1'); // 1)
        $this->addAssessmentAggregated($simulation, '214a3'); // 1)
        $this->addAssessmentAggregated($simulation, '214a4'); // 1)
        $this->addAssessmentAggregated($simulation, '214a5'); // 1)

        $this->addAssessmentAggregated($simulation, '214b0'); // 1)
        $this->addAssessmentAggregated($simulation, '214b1'); // 1)
        $this->addAssessmentAggregated($simulation, '214b2'); // 1)
        $this->addAssessmentAggregated($simulation, '214b3'); // 1)
        //$this->addAssessmentAggregated($simulation, '214b4'); // 1)
        $this->addAssessmentAggregated($simulation, '214b9'); // 1)

        $this->addAssessmentAggregated($simulation, '214b5');
        $this->addAssessmentAggregated($simulation, '214b6');

        $this->addAssessmentAggregated($simulation, '214d5'); // 1) "-"
        $this->addAssessmentAggregated($simulation, '214d6'); // 1) "-"
        $this->addAssessmentAggregated($simulation, '214d8'); // 1) "-"

        $this->addAssessmentAggregated($simulation, '4123'); // 2) "-"
        $this->addAssessmentAggregated($simulation, '4126'); // 2) "-"
        $this->addAssessmentAggregated($simulation, '4151'); // 2) "-"
        //$this->addAssessmentAggregated($simulation, '4134'); // 3. "-"
        //$this->addAssessmentAggregated($simulation, '4135'); // 3. "-"

        $this->addAssessmentAggregated($simulation, '3324', null, 5); // 3) "-"
        $this->addAssessmentAggregated($simulation, '351a2'); // 3) "-"

        $this->addAssessmentAggregated($simulation, '8311', 100);
        $this->addAssessmentAggregated($simulation, '8351', 100);
        $this->addAssessmentAggregated($simulation, '8331', 100);
        $this->addAssessmentAggregated($simulation, '8381', 100);
        $this->addAssessmentAggregated($simulation, '8211', 100); // Выполняет свои обещания (% выполненных обещаний)
        $this->addAssessmentAggregated($simulation, '8212', 100); // Несёт ответственность за свои поступки
        $this->addAssessmentAggregated($simulation, '8213', 100); // Несёт ответственность за своих подчинённых
        $this->addAssessmentAggregated($simulation, '8341', 100);
        $this->addAssessmentAggregated($simulation, '8371', 100);
        $this->addAssessmentAggregated($simulation, '7211', 100);
        $this->addAssessmentAggregated($simulation, '7141', 150); // Stress resistance
        $this->addAssessmentAggregated($simulation, '8391', 150); // Гибкость
        $this->addAssessmentAggregated($simulation, '8111', 150); // Внимательность

        // -------------------------------

        $this->addPerformanceAggregated($simulation, '0'    , 100, 10);
        $this->addPerformanceAggregated($simulation, '1'    , 50, 10);
        $this->addPerformanceAggregated($simulation, '2'    , 10,  10);
        $this->addPerformanceAggregated($simulation, '2_min', 50, 10);

        // ---------------------------

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS, 82);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL, 40);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS, 30);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS, 40);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING, 100);

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS, 50);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL, 42);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS, 36);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS, 46);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING, 0);

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES, 97);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES, 0);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_INACTIVITY, 3);

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_WORKDAY_OVERHEAD_DURATION, 12);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_EFFICIENCY, 50);

        $learningGoalAnalyzer = new LearningGoalAnalyzer($simulation);
        $learningGoalAnalyzer->run();

        $learning_area = new LearningAreaAnalyzer($simulation);
        $learning_area->run();

        $evaluation = new Evaluation($simulation);
        $evaluation->run();

//        $goalGroups = SimulationLearningGoalGroup::model()->findAllByAttributes(['sim_id' => $simulation->id]);
//
//        foreach ($goalGroups as $goalGroup) {
//            echo sprintf(
//                "%s %s | %s %s : %s %s ** %s %s \n",
//                $goalGroup->learningGoalGroup->title,
//                $goalGroup->value,
//                $goalGroup->total_positive,
//                $goalGroup->max_positive,
//                $goalGroup->total_negative,
//                $goalGroup->max_negative,
//                $goalGroup->problem,
//                $goalGroup->getReducingCoefficient()
//            );
//        }
//
//        echo "\n";

        $areas   = SimulationLearningArea::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $overall = AssessmentOverall::model()->findAllByAttributes(['sim_id' => $simulation->id]);

        $v = [
            'Управление задачами с учётом приоритетов' => 88.944443,
            'Управление людьми'         => 90.003998,
            'Управление коммуникациями' => 89.928574,

            'Оптимальный выбор каналов коммуникации' => 100,
            'Устойчивость к манипуляциям и давлению' => 100,
            'Стрессоустойчивость'     => 100,
            'Ответственность'         => 100,
            'Принятие решения'        => 100,
            'Ориентация на результат' => 100,
            'Конструктивность'        => 100,
            'Гибкость'                => 100,
            'Внимательность'          => 100,
        ];

        foreach ($areas as $listItem) {
//            echo sprintf(
//                "%s %s \n",
//                $listItem->learningArea->title,
//                $listItem->value
//            );
            $this->assertEquals(
                $v[$listItem->learningArea->title],
                $listItem->value,
                'Areas: '.$listItem->learningArea->title
            );
        }

        $v = [
            'management'  => 89.51,
            'overall'     => 75.95,
            'performance' => 67.70,
            'time'        => 50,
        ];

//        foreach ($overall as $listItem) {
//            echo sprintf(
//                "%s %s \n",
//                $listItem->assessment_category_code,
//                $listItem->value
//            );
//        }

        foreach ($overall as $listItem) {
            $this->assertEquals(
                $v[$listItem->assessment_category_code],
                $listItem->value,
                'Overals: '.$listItem->assessment_category_code
            );
        }
    }

    /**
     * Максимальное влияние негативных шкал
     */
    public function testAssessment_Goals_Areas_Overals_case3()
    {
        //$this->markTestSkipped(); // wait for new assessment scheme

        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // ---
        $this->addAssessmentAggregated($simulation, '3312');
        $this->addAssessmentAggregated($simulation, '341a8');

        $this->addAssessmentAggregated($simulation, '214d0');
        $this->addAssessmentAggregated($simulation, '214d1');
        $this->addAssessmentAggregated($simulation, '214d2');
        $this->addAssessmentAggregated($simulation, '214d3');
        $this->addAssessmentAggregated($simulation, '214d4');

        $this->addAssessmentAggregated($simulation, '341b5');
        $this->addAssessmentAggregated($simulation, '341b7');
        $this->addAssessmentAggregated($simulation, '4121'); // 8
        $this->addAssessmentAggregated($simulation, '4124'); // 8
        $this->addAssessmentAggregated($simulation, '3216');
        $this->addAssessmentAggregated($simulation, '4122'); // 8
        $this->addAssessmentAggregated($simulation, '341b1');
        $this->addAssessmentAggregated($simulation, '351b3');
        $this->addAssessmentAggregated($simulation, '4125'); // 8
        $this->addAssessmentAggregated($simulation, '4141'); // 8
        $this->addAssessmentAggregated($simulation, '4143'); // 8
        $this->addAssessmentAggregated($simulation, '4153'); // 8
        $this->addAssessmentAggregated($simulation, '4127'); // 8
        $this->addAssessmentAggregated($simulation, '3214');
        $this->addAssessmentAggregated($simulation, '351a2');
        //$this->addAssessmentAggregated($simulation, '1122');
        $this->addAssessmentAggregated($simulation, '1232'); // 1
        $this->addAssessmentAggregated($simulation, '3218'); // 1
        $this->addAssessmentAggregated($simulation, '351b2');
        $this->addAssessmentAggregated($simulation, '351b1');
        $this->addAssessmentAggregated($simulation, '351c1');
        $this->addAssessmentAggregated($simulation, '351c3');
        $this->addAssessmentAggregated($simulation, '351c2');
        $this->addAssessmentAggregated($simulation, '351a1');
        $this->addAssessmentAggregated($simulation, '351a4');
        $this->addAssessmentAggregated($simulation, '351b4');
        $this->addAssessmentAggregated($simulation, '3322');
        $this->addAssessmentAggregated($simulation, '3323');
        $this->addAssessmentAggregated($simulation, '3313');
        $this->addAssessmentAggregated($simulation, '3333');
        $this->addAssessmentAggregated($simulation, '3326');
        $this->addAssessmentAggregated($simulation, '3311');
        $this->addAssessmentAggregated($simulation, '3332');
        $this->addAssessmentAggregated($simulation, '214a1');
        $this->addAssessmentAggregated($simulation, '214a3');
        $this->addAssessmentAggregated($simulation, '214a4');
        $this->addAssessmentAggregated($simulation, '214a5');
        $this->addAssessmentAggregated($simulation, '214b0');
        $this->addAssessmentAggregated($simulation, '214b1');
        $this->addAssessmentAggregated($simulation, '214b2');
        $this->addAssessmentAggregated($simulation, '214b3');
        //$this->addAssessmentAggregated($simulation, '214b4');
        $this->addAssessmentAggregated($simulation, '214b9');

        $this->addAssessmentAggregated($simulation, '214b5', null, 5); // 2
        $this->addAssessmentAggregated($simulation, '214b6', null, 5); // 2
        $this->addAssessmentAggregated($simulation, '214a8', null, 5); // 2, 1 ints. = 100%

        $this->addAssessmentAggregated($simulation, '214d5', null, 5); // 2. "-"
        $this->addAssessmentAggregated($simulation, '214d6', null, 5); // 2. "-"
        $this->addAssessmentAggregated($simulation, '214d8', null, 5); // 2. "-"

        $this->addAssessmentAggregated($simulation, '4123', null, 10); // 3. "-"
        $this->addAssessmentAggregated($simulation, '4126', null, 10); // 3. "-"
        $this->addAssessmentAggregated($simulation, '4151', null, 10); // 3. "-"
        $this->addAssessmentAggregated($simulation, '4134', null, 10); // 3. "-"
        $this->addAssessmentAggregated($simulation, '4135', null, 10); // 3. "-"

        $this->addAssessmentAggregated($simulation, '3324', null, 5); // 5. "-"

        $this->addAssessmentAggregated($simulation, '8311', 100);
        $this->addAssessmentAggregated($simulation, '8351', 100);
        $this->addAssessmentAggregated($simulation, '8331', 100);
        $this->addAssessmentAggregated($simulation, '8381', 100);
        $this->addAssessmentAggregated($simulation, '8211', 100); // Выполняет свои обещания (% выполненных обещаний)
        $this->addAssessmentAggregated($simulation, '8212', 100); // Несёт ответственность за свои поступки
        $this->addAssessmentAggregated($simulation, '8213', 100); // Несёт ответственность за своих подчинённых
        $this->addAssessmentAggregated($simulation, '8341', 100);
        $this->addAssessmentAggregated($simulation, '8371', 100);
        $this->addAssessmentAggregated($simulation, '7211', 100);
        $this->addAssessmentAggregated($simulation, '7141', 150); // Stress resistance
        $this->addAssessmentAggregated($simulation, '8391', 150); // Гибкость
        $this->addAssessmentAggregated($simulation, '8111', 150); // Внимательность

        // -------------------------------

        $this->addPerformanceAggregated($simulation, '0'    , 100, 10);
        $this->addPerformanceAggregated($simulation, '1'    , 50, 10);
        $this->addPerformanceAggregated($simulation, '2'    , 10,  10);
        $this->addPerformanceAggregated($simulation, '2_min', 50, 10);

        // ---------------------------

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS, 82);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL, 40);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS, 30);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS, 40);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING, 100);

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS, 50);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL, 42);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS, 36);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS, 46);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING, 0);

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES, 97);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES, 0);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_INACTIVITY, 3);

        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_WORKDAY_OVERHEAD_DURATION, 12);
        $this->addTimeManagementAggregated($simulation, TimeManagementAggregated::SLUG_EFFICIENCY, 50);

        $learningGoalAnalyzer = new LearningGoalAnalyzer($simulation);
        $learningGoalAnalyzer->run();

        $learning_area = new LearningAreaAnalyzer($simulation);
        $learning_area->run();

        $evaluation = new Evaluation($simulation);
        $evaluation->run();

        $areas   = SimulationLearningArea::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $overall = AssessmentOverall::model()->findAllByAttributes(['sim_id' => $simulation->id]);

        $v = [
            'Управление задачами с учётом приоритетов'  => 40.833332,
            'Управление коммуникациями'                 => 89.928574 ,
            'Управление людьми'                         => 30,
        ];

//        foreach ($areas as $listItem) {
//            echo sprintf(
//                "%s %s \n",
//                $listItem->learningArea->title,
//                $listItem->value
//            );
//        } die;

        foreach ($areas as $listItem) {
            if (isset($v[$listItem->learningArea->title])) {
                $this->assertEquals(
                    $v[$listItem->learningArea->title],
                    $listItem->value,
                    'Areas: '.$listItem->learningArea->title
                );
            }
        }

        $v = [
            'management'  => 55.86,
            'overall'     => 59.13,
            'performance' => 67.70,
            'time'        => 50,
        ];

//        foreach ($overall as $listItem) {
//            echo sprintf(
//                "%s %s \n",
//                $listItem->assessment_category_code,
//                $listItem->value
//            );
//        } die;

        foreach ($overall as $listItem) {
            $this->assertEquals(
                $v[$listItem->assessment_category_code],
                $listItem->value,
                'Overals: '.$listItem->assessment_category_code
            );
        }
    }

    public function testAssessment_Goals_Areas_Overals_case4()
    {
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        // ---

        $this->addAssessmentAggregated($simulation, '4122');
        $this->addAssessmentAggregated($simulation, '4125');

        $learningGoalAnalyzer = new LearningGoalAnalyzer($simulation);
        $learningGoalAnalyzer->run();

        $learning_area = new LearningAreaAnalyzer($simulation);
        $learning_area->run();

        $evaluation = new Evaluation($simulation);
        $evaluation->run();

        // --- Goal

        $goals = SimulationLearningGoal::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        foreach ($goals as $goal) {
            if ('Использовать делегирование как инструмент управления своим временем и объемом выполненных задач' == $goal->learningGoal->title) {
//                echo sprintf(
//                    "%s %s %s \n",
//                    // $goal->learningGoal->title,
//                    $goal->value,
//                    $goal->percent,
//                    '%'
//                );
                $this->assertEquals(50, $goal->percent); // '47.05'
            }
        }

        // --- Areas

        $v = [ 'Управление людьми' => 30 ]; // 27.586206
        $areas   = SimulationLearningArea::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        //echo "\n Areas: \n";
        foreach ($areas as $listItem) {
//            echo sprintf(
//                "%s %s \n",
//                $listItem->learningArea->title,
//                $listItem->value
//            );
            if ('Управление людьми' === $listItem->learningArea->title) {
                $this->assertEquals(
                    $v[$listItem->learningArea->title],
                    $listItem->value,
                    'Areas: '.$listItem->learningArea->title
                );
            }
        }

        // --- Overalls

        $overall = AssessmentOverall::model()->findAllByAttributes(['sim_id' => $simulation->id]);

        $v = [
            'management'  => 6,    // 5.37
            'overall'     => 3,    // 2.69
            'performance' => 0.00,
            'time'        => 0.00,
        ];

        foreach ($overall as $listItem) {
//            echo sprintf(
//                "%s %s \n",
//                $listItem->assessment_category_code,
//                $listItem->value
//            );
            $this->assertEquals(
                $v[$listItem->assessment_category_code],
                $listItem->value,
                'Overals: '.$listItem->assessment_category_code
            );
        }
    }

    public function testAsseessmentPdfGeneration() {
        $user = $this->initTestUserAsd();
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);
        $simulation->status = Simulation::STATUS_COMPLETE;
        $invite->status = Invite::STATUS_COMPLETED;


        $simulation->results_popup_partials_path = '//simulation_details_popup/v2';
        $simulation->results_popup_cache = 'a:7:{s:10:"management";a:4:{i:1;a:5:{s:5:"total";s:9:"27.673653";s:3:"1_1";a:2:{s:1:"+";s:5:"54.54";s:1:"-";s:4:"0.00";}s:3:"1_2";a:2:{s:1:"+";s:5:"34.20";s:1:"-";s:5:"35.00";}s:3:"1_3";a:2:{s:1:"+";s:5:"78.85";s:1:"-";s:5:"23.33";}s:3:"1_4";a:2:{s:1:"+";s:4:"0.00";s:1:"-";s:6:"100.00";}}i:2;a:4:{s:5:"total";s:9:"51.673752";s:3:"2_1";a:2:{s:1:"+";s:5:"56.94";s:1:"-";s:4:"7.14";}s:3:"2_2";a:2:{s:1:"+";s:5:"50.00";s:1:"-";s:5:"15.78";}s:3:"2_3";a:2:{s:1:"+";s:5:"85.71";s:1:"-";s:5:"14.28";}}i:3;a:5:{s:5:"total";s:9:"45.971428";s:3:"3_1";a:2:{s:1:"+";s:6:"100.00";s:1:"-";s:4:"0.00";}s:3:"3_2";a:2:{s:1:"+";s:5:"27.64";s:1:"-";s:4:"0.00";}s:3:"3_3";a:2:{s:1:"+";s:5:"59.83";s:1:"-";s:4:"0.00";}s:3:"3_4";a:2:{s:1:"+";s:5:"42.85";s:1:"-";s:5:"20.00";}}s:5:"total";s:5:"38.87";}s:11:"performance";a:5:{i:0;s:9:"48.750000";i:1;d:31.428571428571427;i:2;d:52.631578947368418;s:5:"total";s:5:"44.26";s:5:"2_min";d:42.857142857142854;}s:4:"time";a:16:{s:5:"total";s:5:"79.33";s:25:"workday_overhead_duration";s:4:"0.00";s:38:"time_spend_for_1st_priority_activities";s:5:"69.00";s:38:"time_spend_for_non_priority_activities";s:5:"13.00";s:25:"time_spend_for_inactivity";s:5:"18.00";s:22:"1st_priority_documents";s:5:"69.00";s:21:"1st_priority_meetings";s:5:"81.00";s:24:"1st_priority_phone_calls";s:5:"71.00";s:17:"1st_priority_mail";s:5:"79.00";s:21:"1st_priority_planning";s:5:"40.00";s:22:"non_priority_documents";s:4:"1.00";s:21:"non_priority_meetings";s:5:"34.00";s:24:"non_priority_phone_calls";s:5:"13.00";s:17:"non_priority_mail";s:5:"16.00";s:21:"non_priority_planning";s:4:"0.00";s:10:"efficiency";s:5:"79.33";}s:7:"overall";s:5:"46.83";s:10:"percentile";a:1:{s:5:"total";s:5:"94.01";}s:8:"personal";a:8:{i:9;s:9:"26.168224";i:10;s:9:"16.666668";i:11;s:9:"12.500000";i:12;s:9:"22.727274";i:13;s:9:"26.724138";i:14;s:9:"45.000000";i:15;s:9:"54.166668";i:16;s:9:"30.000000";}s:15:"additional_data";a:3:{s:10:"management";s:12:"0.5000000000";s:11:"performance";s:12:"0.3500000000";s:4:"time";s:12:"0.1500000000";}}';

        SimulationService::saveAssessmentPDFFilesOnDisk($simulation);

        $this->assertTrue(true);
        //exec("rm ");
    }

    // -----------------------------------------------------
    // Service methods
    // -----------------------------------------------------

    /**
     * @param Simulation $simulation
     * @param $code
     * @return bool
     */
    private function addStressPoint(Simulation $simulation, $code )
    {
        if (is_integer($code)) {
            $stresRule = $simulation->game_type->getStressRule(['code' => $code]);
        } elseif ($code instanceof HeroBehaviour) {
            $stresRule = $code;
        }

        if (null == $stresRule) {
            return false;
        }

        $item = new StressPoint();
        $item->sim_id         = $simulation->id;
        $item->stress_rule_id = $stresRule->id;
        $item->save();
    }

    /**
     * @param Simulation $simulation
     * @param $slug
     * @param $value
     */
    private function addTimeManagementAggregated(Simulation $simulation, $slug, $value )
    {
        $item = new TimeManagementAggregated();
        $item->sim_id     = $simulation->id;
        $item->slug       = $slug;
        $item->value      = $value;
        $item->unit_label = TimeManagementAggregated::getUnitLabel($slug);
        $item->save(false);
    }

    /**
     * @param Simulation $simulation
     * @param $code
     * @param $persents
     * @param $value
     */
    private function addPerformanceAggregated(Simulation $simulation, $code, $persents, $value )
    {
        $category = ActivityCategory::model()->findByAttributes(['code' => $code]);

        $item = new PerformanceAggregated();
        $item->sim_id         = $simulation->id;
        $item->category_id    = $category->getPrimaryKey();
        $item->value          = $value;
        $item->percent        = $persents;
        $item->save(false);
    }

    /**
     * @param Simulation $simulation
     * @param $code
     * @param null $value
     * @param int $k
     * @return AssessmentAggregated|bool
     */
    private function addAssessmentAggregated(Simulation $simulation, $code, $value = null, $k = 1 )
    {
        if (is_string($code)) {
            $behaviour = $simulation->game_type->getHeroBehaviour(['code' => $code]);
        } elseif ($code instanceof HeroBehaviour) {
            $behaviour = $code;
        }

        if (null == $behaviour) {
            return false;
        }
        $value = (null === $value) ? $behaviour->scale : $value;

        $item = new AssessmentAggregated();
        $item->sim_id      = $simulation->id;
        $item->point_id    = $behaviour->id;
        $item->value       = $k * $value;
        $item->save();

        return $item;
    }

    /**
     * @param Simulation $simulation
     * @param $code
     * @param int $k
     * @return bool
     */
    private function addAssessmentPoints(Simulation $simulation, $code, $k = 1 )
    {
        if (is_string($code)) {
            $behaviour = $simulation->game_type->getHeroBehaviour(['code' => $code]);
        } elseif ($code instanceof HeroBehaviour) {
            $behaviour = $code;
        }

        if (null == $behaviour) {
            return false;
        }

        $item = new AssessmentPoint();
        $item->sim_id   = $simulation->id;
        $item->point_id = $behaviour->id;
        $item->value    = $k * $behaviour->scale;
        $item->save(false);
    }

    /**
     * @param Simulation $simulation
     * @param $code
     * @param int $k
     * @return bool
     */
    private function addAssessmentCalculation(Simulation $simulation, $code, $k = 1 )
    {
        if (is_string($code)) {
            $behaviour = $simulation->game_type->getHeroBehaviour(['code' => $code]);
        } elseif ($code instanceof HeroBehaviour) {
            $behaviour = $code;
        }

        if (null == $behaviour) {
            return false;
        }

        $item = new AssessmentCalculation();
        $item->sim_id   = $simulation->id;
        $item->point_id = $behaviour->id;
        $item->value    = $k * $behaviour->scale;
        $item->save();
    }
}