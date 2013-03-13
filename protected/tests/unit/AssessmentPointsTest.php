<?php

class AssessmentPointsTest extends CDbTestCase
{
    use UnitLoggingTrait;

    /**
     * Checks that user gains points for sent mail only once
     */
    public function testMailPointUnique()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        $mgr = new EventsManager();
        $logs = [];
        $template = MailTemplate::model()->byCode('MS20')->find();

        $message = LibSendMs::sendMs($simulation, 'MS20');
        $this->appendNewMessage($logs, $message);
        $mgr->processLogs($simulation, $logs);

        $points = AssessmentPoint::model()->countByAttributes([
            'sim_id' => $simulation->id,
            'mail_id' => $template->id
        ]);

        // Send again
        $message = LibSendMs::sendMs($simulation, 'MS20');
        $this->appendNewMessage($logs, $message);

        $newPoints = AssessmentPoint::model()->countByAttributes([
            'sim_id' => $simulation->id,
            'mail_id' => $template->id
        ]);

        $this->assertEquals($points, $newPoints);
    }
}