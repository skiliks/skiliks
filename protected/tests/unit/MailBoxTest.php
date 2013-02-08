<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 06.02.13
 * Time: 12:18
 * To change this template use File | Settings | File Templates.
 */
class MailBoxTest extends CDbTestCase
{
    public function testSubjects() 
    {
        // $this->markTestSkipped();
        
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);
        
        $mail = new MailBoxService();
        $events = new EventsManager();
        $character = Characters::model()->findByAttributes(['code' => 9]);

        $message = $mail->sendMessage([
            'subject_id' => CommunicationTheme::model()->findByAttributes(['code' => 5, 'character_id' => $character->primaryKey, 'mail_prefix' => 're'])->primaryKey,
            'message_id' => MailTemplateModel::model()->findByAttributes(['code' => 'MS40'])->primaryKey,
            'receivers' => $character->primaryKey,
            'sender' => Characters::model()->findByAttributes(['code' => 1])->primaryKey,
            'copies' => implode(',',[
                Characters::model()->findByAttributes(['code' => 2])->primaryKey,
                Characters::model()->findByAttributes(['code' => 11])->primaryKey,
                Characters::model()->findByAttributes(['code' => 12])->primaryKey,
            ]),
            'time' => '11:00:00',
            'group' => 3,
            'letterType' => 'new',
            'simId' => $simulation->primaryKey
        ]);
        $events->startEvent($simulation->id,'M31', false, false,0);
        $events->getState($simulation, []);
        $folders = MailBoxService::getFolders($simulation);
        $this->assertEquals(count($folders),2);
        $this->assertEquals(count($folders[0]),4);
        $this->assertEquals(count($folders[1]),2);
        $this->assertEquals(count($folders[1]['sended']),2);
        $inbox_letters = array_values($folders[1]['inbox']);
        $sent_letters = array_values($folders[1]['sended']);
        
        $this->assertEquals('Re: срочно! Отчетность', $sent_letters[1]['subject']);
        $this->assertEquals('Форма отчетности для производства', $inbox_letters[1]['subject']);
        $this->assertEquals('Re: Срочно жду бюджет логистики', $inbox_letters[4]['subject']);

    }
    
    public function testSubjectsForReReCase() 
    {
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user);
        
        $mail = new MailBoxService();
        $events = new EventsManager();
        $character = Characters::model()->findByAttributes(['code' => 9]);

        $message = $mail->sendMessage([
            'subject_id' => CommunicationTheme::model()->findByAttributes(['code' => 5, 'character_id' => $character->primaryKey, 'mail_prefix' => 're'])->primaryKey,
            'message_id' => MailTemplateModel::model()->findByAttributes(['code' => 'MS40'])->primaryKey,
            'receivers'  => $character->primaryKey,
            'sender'     => Characters::model()->findByAttributes(['code' => 1])->primaryKey,
            'copies'     => implode(',',[
                Characters::model()->findByAttributes(['code' => 2])->primaryKey,
                Characters::model()->findByAttributes(['code' => 11])->primaryKey,
                Characters::model()->findByAttributes(['code' => 12])->primaryKey,
            ]),
            'time' => '11:00:00',
            'group' => 3,
            'letterType' => 'new',
            'simId' => $simulation->primaryKey
        ]);
        
        $events->startEvent($simulation->id, 'M31', false, false,0);
        
        $messageToReply = MailBoxModel::model()->findByAttributes([
            'sim_id' => $simulation->id, 
            'code' => 'M31'
        ]);

        $subjectEntity = MailBoxService::getSubjectForRepryEmail($messageToReply);
        
        $this->assertNotNull($subjectEntity);
        
        $this->assertEquals('Re: Re: Срочно жду бюджет логистики', $subjectEntity->getFormattedThemePrefix());
    }    
    
    /**
     * 
     */
    public function testForward() 
    {
        // $this->markTestSkipped();
        
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        $randomFirstEmail = MailBoxModel::model()->find('sim_id = :sim_id', ['sim_id' => $simulation->id]);
        
        $resultData = MailBoxService::getForwardMessageData($simulation, $randomFirstEmail);
        
        $this->assertEquals($resultData['subject'], 'Fwd: '.$randomFirstEmail->subject_obj->text);
    }
}
