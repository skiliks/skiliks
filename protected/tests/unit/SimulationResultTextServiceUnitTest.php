<?php

class SimulationResultTextServiceUnitTest extends CDbTestCase {

    public function testGenerate(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);
        $simulation->results_popup_cache = 'a:7:{s:10:"management";a:4:{s:5:"total";s:5:"31.49";i:1;a:5:{s:5:"total";s:9:"21.097778";s:3:"1_1";a:2:{s:1:"+";s:5:"15.09";s:1:"-";s:4:"0.00";}s:3:"1_2";a:2:{s:1:"+";s:5:"19.20";s:1:"-";s:5:"50.00";}s:3:"1_3";a:2:{s:1:"+";s:5:"73.71";s:1:"-";s:5:"40.00";}s:3:"1_4";a:2:{s:1:"+";s:4:"0.00";s:1:"-";s:5:"80.00";}}i:3;a:5:{s:5:"total";s:9:"44.931431";s:3:"3_1";a:2:{s:1:"+";s:5:"55.55";s:1:"-";s:4:"0.00";}s:3:"3_2";a:2:{s:1:"+";s:5:"39.47";s:1:"-";s:4:"0.00";}s:3:"3_3";a:2:{s:1:"+";s:5:"55.00";s:1:"-";s:4:"0.00";}s:3:"3_4";a:2:{s:1:"+";s:5:"52.38";s:1:"-";s:5:"20.00";}}i:2;a:4:{s:5:"total";s:9:"31.360800";s:3:"2_1";a:2:{s:1:"+";s:5:"10.00";s:1:"-";s:5:"21.42";}s:3:"2_2";a:2:{s:1:"+";s:5:"66.66";s:1:"-";s:5:"10.52";}s:3:"2_3";a:2:{s:1:"+";s:5:"87.50";s:1:"-";s:4:"0.00";}}}s:11:"performance";a:5:{s:5:"total";s:5:"19.13";i:0;s:9:"16.250000";i:1;s:9:"20.000000";i:2;s:9:"15.789474";s:5:"2_min";s:9:"24.489796";}s:4:"time";a:16:{s:5:"total";s:5:"75.61";s:25:"workday_overhead_duration";s:5:"11.00";s:38:"time_spend_for_1st_priority_activities";s:5:"68.00";s:38:"time_spend_for_non_priority_activities";s:5:"22.00";s:25:"time_spend_for_inactivity";s:5:"10.00";s:22:"1st_priority_documents";s:5:"78.00";s:21:"1st_priority_meetings";s:5:"96.00";s:24:"1st_priority_phone_calls";s:5:"69.00";s:17:"1st_priority_mail";s:5:"47.00";s:21:"1st_priority_planning";s:5:"54.00";s:22:"non_priority_documents";s:4:"4.00";s:21:"non_priority_meetings";s:5:"78.00";s:24:"non_priority_phone_calls";s:5:"22.00";s:17:"non_priority_mail";s:4:"6.00";s:21:"non_priority_planning";s:4:"0.00";s:10:"efficiency";s:5:"75.61";}s:7:"overall";s:5:"33.78";s:10:"percentile";a:1:{s:5:"total";s:5:"52.28";}s:8:"personal";a:8:{i:9;s:9:"14.018691";i:10;s:9:"33.333332";i:12;s:9:"13.636364";i:13;s:9:"40.632183";i:14;s:9:"20.000000";i:15;s:9:"25.000000";i:16;s:9:"50.000000";i:11;s:9:"25.000000";}s:15:"additional_data";a:3:{s:10:"management";s:12:"0.5000000000";s:11:"performance";s:12:"0.3500000000";s:4:"time";s:12:"0.1500000000";}}';
        $simulation->save(false);

        $recommendations = SimulationResultTextService::generate($simulation, 'popup');

        $this->assertEquals([
            'Ваши результаты нормальные!',
            'Вы не планируете!',
            'Вы не ошибаетесь при планировании. Вам пора на повышение?'
        ], $recommendations);
    }

}
 