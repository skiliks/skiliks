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
    public function testSubjectsForInitialEmails() 
    {
        //$this->markTestSkipped();
        
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
        $inbox_letters = array_values($folders[1]['inbox']);
        $sent_letters = array_values($folders[1]['sended']);
        
        foreach ($inbox_letters as $inbox_letter) {
            if ('форма отчетности для производства' == $inbox_letter['subjectSort']) {
                $m1 = $inbox_letter;
            }
            if (' срочно жду бюджет логистики' == $inbox_letter['subjectSort']) {
                $m2 = $inbox_letter;
            }
        }
        
        $this->assertEquals('Re: срочно! Отчетность', $sent_letters[1]['subject']);
        $this->assertEquals('Форма отчетности для производства', $m1['subject']);
        $this->assertEquals('Re: Срочно жду бюджет логистики', $m2['subject']);

    }
    
    public function testSubjectForNewEmail() 
    {
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(1, $user); 
        
        // one recipient case :
        $subjects = MailBoxService::getThemes('11', NULL); 
        $id = CommunicationTheme::getCharacterThemeId('11', 0);
        
        $this->assertEquals(count($subjects), 3);
        $this->assertTrue(in_array('Бюджет производства прошлого года', $subjects));
        $this->assertTrue(in_array('Бюджет производства 02: коррективы', $subjects));
        $this->assertTrue(in_array('Прочее', $subjects));
        
        $this->assertNull($id);
        
        // several recipients case :
        $subjects_2 = MailBoxService::getThemes('11,26,24', NULL); 
        $id_2 = CommunicationTheme::getCharacterThemeId('11', 0);
        
        $this->assertEquals(count($subjects_2), 3);
        $this->assertTrue(in_array('Бюджет производства прошлого года', $subjects_2));
        $this->assertTrue(in_array('Бюджет производства 02: коррективы', $subjects_2));
        $this->assertTrue(in_array('Прочее', $subjects_2));
        
        $this->assertNull($id_2);
    }
    
    public function testSubjectsForReReCase() 
    {
        //$this->markTestSkipped();
        
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
        
        $this->assertEquals('Re: Re: Срочно жду бюджет логистики', $subjectEntity->getFormattedTheme());
    }    
    
    /**
     * 
     */
    public function testForward() 
    {
        //$this->markTestSkipped();
        
        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        
        // random email case{       
        $randomFirstEmail = MailBoxModel::model()->find('sim_id = :sim_id', ['sim_id' => $simulation->id]);        
        $resultData = MailBoxService::getForwardMessageData($simulation, $randomFirstEmail);      
        
        $this->assertEquals($resultData['subject'], 'Fwd: '.$randomFirstEmail->subject_obj->text, 'random email case');
        // random email case }
        
        // case 2, M61 {      
        $email = MailBoxModel::model()->findByAttributes(['sim_id' => $simulation->id, 'code' => 'M61']);
        $email->group_id = 1;
        $email->save();        
        $resultDataM61 = MailBoxService::getForwardMessageData($simulation, $email);
        
        $this->assertEquals($resultDataM61['subject'], 'Fwd: Re: '.$email->subject_obj->text, 'M61');
        // case 2, M61 }
    }

    public function testGetPhrases() {
        //$simulation_service = new SimulationService();
        //$user = Users::model()->findByAttributes(['email' => 'asd']);
        //$simulation = $simulation_service->simulationStart(1, $user);
        $ch = Characters::model()->findByAttributes(['fio'=>'Денежная Р.Р.']);
        $theme = CommunicationTheme::model()->findByAttributes(['character_id'=>$ch->id,'text'=>'Сводный бюджет', 'letter_number'=>'MS35']);
        $mail_phrases = MailPhrasesModel::model()->findAllByAttributes(['code'=>'R1']);
        $data= [];
        foreach($mail_phrases as $phrase){
            $data[$phrase->id] = $phrase->name;
        }
        $phrases = MailBoxService::getPhrases($theme->id, 0);
        $this->assertEquals($data, $phrases['data']);
    }
}

