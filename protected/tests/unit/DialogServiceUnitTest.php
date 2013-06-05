<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Тугай
 * Date: 06.02.13
 * Time: 18:37
 * To change this template use File | Settings | File Templates.
 */

class DialogServiceUnitTest extends PHPUnit_Framework_TestCase
{

    public function testDialogGet()
    {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $standard = [
            'result' => 1,
            'events' => []
        ];

        $res = (new DialogService())->getDialog(
            $simulation->id,
            Replica::model()->findByAttributes(['code' => 'ET1.1', 'replica_number' => 2])->id,
            '11:00');

        $this->assertEquals($res, $standard);

        $this->assertEquals(
            (new DialogService())->getDialog(
                $simulation->id,
                Replica::model()->findByAttributes(['code' => 'S1.1', 'replica_number' => 2])->id,
                '11:05'),
            $standard
        );

    }

    public function testDialogGetForDialogAndPlan()
    {
        $this->markTestIncomplete();
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $standard = json_decode('{"result":1,"events":[{"result":1,"data":[{"id":"357","ch_from":"26","ch_to":"1","dialog_subtype":"4","text":" \u2014 \u0422\u0430\u043a \u043a\u043e\u0433\u0434\u0430, \u0433\u043e\u0432\u043e\u0440\u0438\u0442\u0435, \u044f \u0441\u043c\u043e\u0433\u0443 \u043f\u043e\u043b\u0443\u0447\u0438\u0442\u044c \u0434\u0430\u043d\u043d\u044b\u0435? \u041d\u0430\u043f\u043e\u043c\u0438\u043d\u0430\u044e, \u044d\u0442\u043e \u043f\u0440\u043e\u0435\u043a\u0442 \u0411\u043e\u0441\u0441\u0430. \u0427\u0442\u043e \u043a\u0430\u0441\u0430\u0435\u0442\u0441\u044f \u0434\u043e\u043f\u0443\u0441\u043a\u043e\u0432 \u043a \u0438\u043d\u0444\u043e\u0440\u043c\u0430\u0446\u0438\u0438 \u2013 \u043d\u0435 \u0432\u043e\u043b\u043d\u0443\u0439\u0442\u0435\u0441\u044c, \u0443 \u043c\u0435\u043d\u044f \u0432\u0441\u0435 \u0435\u0441\u0442\u044c. \u0410 \u0438\u043d\u0430\u0447\u0435 \u043a\u0430\u043a \u0431\u044b \u044f \u0443 \u0432\u0430\u0441 \u0442\u0443\u0442 \u043e\u043a\u0430\u0437\u0430\u043b\u0430\u0441\u044c...\u0445\u0430-\u0445\u0430-\u0445\u0430. \u0418\u0442\u0430\u043a, \u043f\u0440\u043e\u0448\u0443 \u043e\u043f\u0440\u0435\u0434\u0435\u043b\u0438\u0442\u044c \u0441\u0440\u043e\u043a\u0438 \u043f\u043e\u0434\u0433\u043e\u0442\u043e\u0432\u043a\u0438 \u0434\u0430\u043d\u043d\u044b\u0445 \u0438 \u0441 \u043a\u0435\u043c \u043c\u043d\u0435 \u043a\u043e\u043d\u0442\u0430\u043a\u0442\u0438\u0440\u043e\u0432\u0430\u0442\u044c \u0432 \u0432\u0430\u0448\u0435 \u043e\u0442\u0441\u0443\u0442\u0441\u0442\u0432\u0438\u0435!","sound":"E3_5_6_Final.webm","step_number":"6","is_final_replica":"0","code":"E3.5","excel_id":"260","title":"\u041a\u043e\u043d\u0441\u0443\u043b\u044c\u0442\u0430\u043d\u0442","name":"\u0410\u043d\u0436\u0435\u043b\u0430 \u0411\u043b\u0435\u0441\u043a","remote_title":"\u041d\u0430\u0447\u0430\u043b\u044c\u043d\u0438\u043a \u041e\u0410\u0438\u041f","remote_name":"\u0424\u0435\u0434\u043e\u0440\u043e\u0432 \u0410.\u0412."},{"id":"358","ch_from":"1","ch_to":"26","dialog_subtype":"4","text":" \u2014 \u041d\u0438\u0447\u0435\u0433\u043e \u043f\u043e\u043a\u0430 \u0441\u043a\u0430\u0437\u0430\u0442\u044c \u043d\u0435 \u043c\u043e\u0433\u0443. \u041d\u0430\u0434\u043e \u043f\u043e\u0434\u0443\u043c\u0430\u0442\u044c. \u0417\u0432\u043e\u043d\u0438\u0442\u0435 \u043c\u043d\u0435 \u043f\u043e\u0441\u043b\u0435 \u043e\u0442\u043f\u0443\u0441\u043a\u0430.","sound":"E3_5_6_Final.jpeg","step_number":"6","is_final_replica":"0","code":"E3.5","excel_id":"261"},{"id":"359","ch_from":"1","ch_to":"26","dialog_subtype":"4","text":" \u2014 \u0421 \u0414\u0435\u043d\u0435\u0436\u043d\u043e\u0439 \u0438 \u043a\u043e\u043d\u0442\u0430\u043a\u0442\u0438\u0440\u0443\u0439\u0442\u0435. \u0410 \u0441\u0440\u043e\u043a\u0438 \u2013 \u043f\u043e\u043a\u0430 \u043d\u0435 \u0437\u043d\u0430\u044e. \u041f\u044f\u0442\u044c \u043b\u0435\u0442 \u2013 \u0434\u0435\u043b\u043e \u0441\u0435\u0440\u044c\u0435\u0437\u043d\u043e\u0435, \u043d\u0430\u0434\u043e \u0441\u0442\u0430\u0440\u044b\u0435 \u0431\u0430\u0437\u044b \u0434\u043e\u0441\u0442\u0430\u0432\u0430\u0442\u044c. \u0412\u0441\u0435 \u0432\u043e\u043f\u0440\u043e\u0441\u044b \u2013 \u043a \u043d\u0435\u0439.","sound":null,"step_number":"6","is_final_replica":"0","code":"E3.5","excel_id":"262"},{"id":"360","ch_from":"1","ch_to":"26","dialog_subtype":"4","text":" \u2014 \u0414\u0430\u043d\u043d\u044b\u0435 \u0434\u043b\u044f \u0432\u0430\u0441 \u043f\u043e\u0434\u0433\u043e\u0442\u043e\u0432\u0438\u0442 \u041c\u0430\u0440\u0438\u043d\u0430 \u041a\u0440\u0443\u0442\u044c\u043a\u043e. \u0421\u0440\u043e\u043a\u0438 - \u0434\u0443\u043c\u0430\u044e, \u0447\u0442\u043e \u043d\u0435 \u043c\u0435\u043d\u044c\u0448\u0435 \u043d\u0435\u0434\u0435\u043b\u0438 \u043f\u043e\u0442\u0440\u0435\u0431\u0443\u0435\u0442\u0441\u044f. \u0421\u0432\u044f\u0436\u0438\u0442\u0435\u0441\u044c \u0441 \u043d\u0435\u0439.","sound":null,"step_number":"6","is_final_replica":"0","code":"E3.5","excel_id":"263"}],"eventType":1}]}', true);

        $res = (new DialogService())->getDialog(
            $simulation->id,
            Replica::model()->findByAttributes(['code' => 'E3.5', 'step_number'=>5, 'replica_number' => 1])->id,
            '11:00');

        $this->assertEquals($standard, $res);

        $template = $simulation->game_type->getEventSample(['code'=>'P30']);

        $ev = EventTrigger::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'event_id' => $template->id
        ]);

        /* @var $ev EventTrigger */

        $this->assertEquals("09:47", date("H:i", strtotime($ev->trigger_time)));
    }



}
