<?php

/**
 *
 * @author slavka
 */
class SimulationServiceTest extends CDbTestCase
{
    /**
     * Проверяет правильность оценивания игрока по за поведение 1122 
     * (оценивание обычным способом, лог писем пуст) 
     * оценка = максимальный_балл * (количество_правильных_проявления / количество_проявления_по_поведения_в_целом)
     */
    public function testCalculateAgregatedPointsFor1122() 
    {
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        // init conts
        $numberOf_1 = 10;
        $numberOf_0 = 14;
        
        // get all replics that change score for behaviour '1122'
        $replicsFor_1122 = Dialogs::model()->findAll('excel_id IN (210, 214, 311, 323, 424, 710, 714, 715, 766, 770, 211, 213, 235, 312, 322, 423, 521, 653, 656, 711, 713, 767, 769, 771)');
        
        $count_0 = 0;
        $count_1 = 0;
        
        // get 1122
        $pointFor_1122 = CharactersPointsTitles::model()->find('code = :code', ['code' => '1122']);  
        
        // init logs
        foreach($replicsFor_1122 as $dialogEntity) {
            LogHelper::setLogDoialogPoint( $dialogEntity->id, $simulation->id, $pointFor_1122->id);
            
            $dialogsPoint = CharactersPoints::model()->find('dialog_id = :dialog_id AND point_id = :point_id',[
                'dialog_id' => $dialogEntity->id,
                'point_id'  => $pointFor_1122->id
            ]);
            
            if ($dialogsPoint->add_value === '1') {
                $count_1++;
            }
            if ($dialogsPoint->add_value === '0') { // not else!
                $count_0++;
            }
        }
        
        // calculate point total scores
        SimulationService::saveAgregatedPoints($simulation->id);
        
        // check calculation
        $assessments = AssessmentAggregated::model()->findAllInSimulation($simulation);
        
        // assertions:
        $this->assertEquals(count($replicsFor_1122), ($numberOf_0 + $numberOf_1), 'Wrong replics number!');
        
        $this->assertEquals($count_0, $numberOf_0, 'Wrong 0 replics number!');
        $this->assertEquals($count_1, $numberOf_1, 'Wrong 1 replics number!');
        
        $this->assertNotEquals(count($assessments), 0, 'No assessments!');
        
        $is_1122_scored = false;
        
        foreach ($assessments as $assessment) {  
            if ($assessment->point->code === '1122') {
                // check 1122 is rigth
                // floor because 8.3333 is not equals to 8.3333333333333 in phpunit! :)
                $this->assertEquals(
                    floor($assessment->value*1000), 
                    floor(($numberOf_1/($numberOf_0 + $numberOf_1))*$assessment->point->scale*1000), 
                    'Wrong 1122 value'
               );
               $is_1122_scored = true;
            } else {
                // check outer points is 0
                $this->assertEquals($assessment->value, 0, 'Wrong not acted behaviour value');
            }
        }
        
        $this->assertTrue($is_1122_scored, '1122 not scored!');
    }
}

