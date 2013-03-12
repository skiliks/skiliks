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
    /**
     * 1. Проверяет темы для письма которое инициализитуются при старте симуляции,
     *    с темой "Форма отчетности для производства"
     * 2. Проверяет тему письма M31 "Re: Срочно жду бюджет логистики"
     * 3. Проверяет тему у MS письма "Re: срочно! Отчетность"
     * 3. Проверяет тему у MSY письма "Отчет для Правления"
     */
    public function testSubjectsForInitialEmails() 
    {
        //$this->markTestSkipped();
        
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(1, $user);

        $character = Character::model()->findByAttributes(['code' => 9]);

        $options = new SendMailOptions();
        $options->phrases = '';
        $options->copies = implode(',',[
            Character::model()->findByAttributes(['code' => 2])->primaryKey,
            Character::model()->findByAttributes(['code' => 11])->primaryKey,
            Character::model()->findByAttributes(['code' => 12])->primaryKey,
        ]);
        $options->messageId = MailTemplate::model()->findByAttributes(['code' => 'MS40'])->primaryKey;
        $options->subject_id = CommunicationTheme::model()->findByAttributes(['code' => 5, 'character_id' => $character->primaryKey, 'mail_prefix' => 're'])->primaryKey;
        $options->setRecipientsArray($character->primaryKey);
        $options->senderId = Character::HERO_ID;
        $options->time = '11:00:00';
        $options->setLetterType('new');
        $options->groupId = MailBox::FOLDER_OUTBOX_ID;
        $options->simulation = $simulation;

        // send MS40
        MailBoxService::sendMessagePro($options);

        FlagsService::setFlag($simulation, 'F30', 1);

        EventsManager::startEvent($simulation,'M31', false, false,0);
        EventsManager::getState($simulation, []);

        // get letters from golders to checl them {
        $folderInbox = MailBoxService::getMessages([
            'folderId'   => MailBox::FOLDER_INBOX_ID,
            'simId'      => $simulation->id
        ]);

        $folderOutbox = MailBoxService::getMessages([
            'folderId'   => MailBox::FOLDER_OUTBOX_ID,
            'simId'      => $simulation->id
        ]);

        $folderDrafts = MailBoxService::getMessages([
            'folderId'   => MailBox::FOLDER_DRAFTS_ID,
            'simId'      => $simulation->id
        ]);

        $folderTrash = MailBoxService::getMessages([
            'folderId'   => MailBox::FOLDER_TRASH_ID,
            'simId'      => $simulation->id
        ]);
        // get letters from golders to checl them }

        $this->assertEquals(5, count($folderInbox));
        $this->assertEquals(2, count($folderOutbox));
        $this->assertEquals(0, count($folderDrafts));
        $this->assertEquals(0, count($folderTrash));

        $inbox_letters = array_values($folderInbox);
        $sent_letters = array_values($folderOutbox);

        // find target messages to check by template code {
        foreach ($inbox_letters as $inbox_letter) {
            if ('MY2' == $inbox_letter['template']) {
                $m1 = $inbox_letter;
            }
            if ('M31' == $inbox_letter['template']) {
                $m2 = $inbox_letter;
            }
        }
        // find target messages to check by template code }

        $this->assertEquals('Отчет для Правления', $sent_letters[0]['subject']);
        $this->assertEquals('Re: срочно! Отчетность', $sent_letters[1]['subject']);

        $this->assertEquals('Форма отчетности для производства', $m1['subject']);
        $this->assertEquals('Re: Срочно жду бюджет логистики', $m2['subject']);

    }


    /**
     * 1. Проверяет темы для нового письма к Василию Бобру.
     * Проверяет что тем 3, и что это темы
     *  - 'Бюджет производства прошлого года'
     *  - 'Бюджет производства 02: коррективы'
     *  - 'Прочее'
     *
     * 2. Проверяет темы для нового письма к Василию Бобру и ещё двум получателям одновременно.
     * Проверяет что тем 3, и что это темы
     *  - 'Бюджет производства прошлого года'
     *  - 'Бюджет производства 02: коррективы'
     *  - 'Прочее'
     * Кейс 2 нужен чтоб видеть, что для выбора тем имеет значение только первый получатель
     */
    public function testSubjectForNewEmail() 
    {
        //
        $bossSubjects = array_values(MailBoxService::getThemes('6', NULL));
        // Check for no duplicates in theme list
        $this->assertEquals(count($bossSubjects), count(array_unique($bossSubjects)));
        // one recipient case :
        $subjects = MailBoxService::getThemes('11', NULL); 
        $id = CommunicationTheme::getCharacterThemeId('11', 0);
        
        $this->assertEquals(count($subjects), 3);
        $this->assertTrue(in_array('Бюджет производства прошлого года', $subjects));
        $this->assertTrue(in_array('Бюджет производства 02: коррективы', $subjects));
        $this->assertTrue(in_array('Прочее', $subjects));
        
        $this->assertNull($id);
        
        // several recipients case :
        $subjects2 = MailBoxService::getThemes('11,26,24', NULL);
        $id2 = CommunicationTheme::getCharacterThemeId('11', 0);
        
        $this->assertEquals(count($subjects2), 3);
        $this->assertTrue(in_array('Бюджет производства прошлого года', $subjects2));
        $this->assertTrue(in_array('Бюджет производства 02: коррективы', $subjects2));
        $this->assertTrue(in_array('Прочее', $subjects2));
        
        $this->assertNull($id2);
    }

    /**
     * 1. Проверяет тему письма, которое является ответом на M31.
     * Тема нового письма должна быть 'Re: Re: Срочно жду бюджет логистики'
     */
    public function testSubjectsForReReCase() 
    {
        //$this->markTestSkipped();
        
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(1, $user);
        
        $character = Character::model()->findByAttributes(['code' => 9]);

        $options = new SendMailOptions();
        $options->phrases = '';
        $options->copies = implode(',',[
            Character::model()->findByAttributes(['code' => 2])->primaryKey,
            Character::model()->findByAttributes(['code' => 11])->primaryKey,
            Character::model()->findByAttributes(['code' => 12])->primaryKey,
        ]);
        $options->messageId = MailTemplate::model()->findByAttributes(['code' => 'MS40'])->primaryKey;
        $options->subject_id = CommunicationTheme::model()->findByAttributes(['code' => 5, 'character_id' => $character->primaryKey, 'mail_prefix' => 're'])->primaryKey;
        $options->setRecipientsArray($character->primaryKey);
        $options->senderId = Character::HERO_ID;
        $options->time = '11:00:00';
        $options->setLetterType('new');
        $options->groupId = MailBox::FOLDER_OUTBOX_ID;
        $options->simulation = $simulation;

        EventsManager::startEvent($simulation, 'M31', false, false,0);

        MailBoxService::copyMessageFromTemplateByCode($simulation, 'M31');
        
        $messageToReply = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id, 
            'code' => 'M31'
        ]);

        $subjectEntity = MailBoxService::getSubjectForRepryEmail($messageToReply);
        
        $this->assertNotNull($subjectEntity);
        
        $this->assertEquals('Re: Re: Срочно жду бюджет логистики', $subjectEntity->getFormattedTheme());
    }    
    
    /**
     * Проверяет темы для писем-перенаправлений:
     * 1. Проверяет что для случайнио выбранного письма из списка писем с темой "ххх",
     *    тема форварда будет выглядеть как "Fwd: ххх"
     *
     * 2. M61 - форвард для письма с одним Re:
     *
     * 3. M62 - форвард для письма с двумя Re:
     */
    public function testForward() 
    {
        //$this->markTestSkipped();
        
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        // random email case{
        $randomFirstEmail = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M8');
        $resultData = MailBoxService::getForwardMessageData($randomFirstEmail);

        $this->assertEquals($resultData['subject'], 'Fwd: '.$randomFirstEmail->subject_obj->text, 'random email case');
        $this->assertEquals($resultData['parentSubjectId'], $randomFirstEmail->subject_obj->id, 'random email case');
        // random email case }

        // case 2, M61 {      
        $emailM61 = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M61');
        $resultDataM61 = MailBoxService::getForwardMessageData($emailM61);

        $this->assertEquals($resultDataM61['subject'], 'Fwd: Re: '.$emailM61->subject_obj->text, 'M61');
        $this->assertEquals($resultDataM61['parentSubjectId'], $emailM61->subject_obj->id, 'M61');
        
        $subject = MailBoxService::getThemes('18', $emailM61->subject_id);
        // case 2, M61 }
        
        // case 3, M62 {
        $emailM62 = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M62');
        $resultDataM62 = MailBoxService::getForwardMessageData($emailM62);
        
        $this->assertEquals($resultDataM62['subject'], 'Fwd: Re: Re: '.$emailM62->subject_obj->text, 'M62');
        $this->assertEquals($resultDataM62['parentSubjectId'], $emailM62->subject_obj->id, 'M62');
        
        $subject = MailBoxService::getThemes('18', $emailM62->subject_id);
        // case 3, M62 }
    }

    /**
     * 1. Проверяет что для нового письма Денежной на тему "'Сводный бюджет" возвращается непустой набор
     *    правильных фраз
     */
    public function testGetPhrases()
    {      
        //$this->markTestSkipped();
        
        $ch = Character::model()->findByAttributes(['fio'=>'Денежная Р.Р.']);
        $theme = CommunicationTheme::model()->findByAttributes(['character_id'=>$ch->id,'text'=>'Сводный бюджет', 'letter_number'=>'MS35']);
        $mail_phrases = MailPhrase::model()->findAllByAttributes(['code'=>'R1']);
        $data= [];
        
        foreach($mail_phrases as $phrase){
            $data[$phrase->id] = $phrase->name;
        }
        
        $phrases = MailBoxService::getPhrases($theme->id, 0);
        $this->assertNotEmpty($data);
        $this->assertEquals($data, $phrases['data']);
        $this->assertEquals(count($data), count($phrases['data']));

        foreach ($phrases['data'] as $phrase) {
            $this->assertTrue(in_array($phrase, $data));
        }
    }

    /**
     * Проверяет что для письма Трутнев С. на тему "FWD:форма по задаче от логистики, срочно!" возвращается непустой набор
     * правильных фраз и равен R6
     *
     */
    public function testGetPhrasesFWD()
    {
        //$this->markTestSkipped();

        $ch = Character::model()->findByAttributes(['fio'=>'Трутнев С.']);
        $theme = CommunicationTheme::model()->findByAttributes(['character_id'=>$ch->id,'text'=>'форма по задаче от логистики, срочно!', 'letter_number'=>'MS42']);
        $mail_phrases = MailPhrase::model()->findAllByAttributes(['code'=>'R6']);
        $data= [];

        foreach($mail_phrases as $phrase){
            $data[$phrase->id] = $phrase->name;
        }

        $phrases = MailBoxService::getPhrases(0, $theme->id);

        $this->assertEquals($data, $phrases['data']);
        $this->assertEquals(count($data), count($phrases['data']));

        foreach ($phrases['data'] as $phrase) {
            $this->assertTrue(in_array($phrase, $data));
        }
    }

    /**
     * Обнаружен баг с разпознание писем у которых конструктор ТХТ
     * Тест предназначен чтоб выявить и устранить причину этого бага
     */
    public function testMailCoincidenceForEmailWithTxtConstructor()
    {
        //$this->markTestSkipped();
        
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        // init conts
        // get all replics that change score for behaviour '4124'
        $replicsFor_4124 = Replica::model()->findAll('excel_id IN (332, 336)');

        $count_0 = 0;
        $count_1 = 0;

        // get 4124
        $pointFor_4124 = HeroBehaviour::model()->find('code = :code', ['code' => '4124']);

        // init dialog logs
        foreach($replicsFor_4124 as $dialogEntity) {
            LogHelper::setLogDialogPoint( $dialogEntity->id, $simulation->id, $pointFor_4124->id);

            $dialogsPoint = ReplicaPoint::model()->find('dialog_id = :dialog_id AND point_id = :point_id',[
                'dialog_id' => $dialogEntity->id,
                'point_id'  => $pointFor_4124->id
            ]);

            if ($dialogsPoint->add_value === '1') {
                $count_1++;
            }
            if ($dialogsPoint->add_value === '0') { // not else!
                $count_0++;
            }
        }
        $this->assertEquals(count($replicsFor_4124), ($count_0 + $count_1), 'Wrong replics add_value values!');

        // init inbox email from sysadmin
        $emailFromSysadmin = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M8');

        // init MS emails:
        // MS27 {
        $subject = CommunicationTheme::model()->find(
            'text = :text AND letter_number = :letter_number',[
            'text'          => '!проблема с сервером!',
            'letter_number' => 'MS27'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('3'); // Трутнев
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = $emailFromSysadmin->id;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;

        $ms_27 = MailBoxService::sendMessagePro($sendMailOptions);
        // MS27 }

        $this->assertEquals('MS27', $ms_27->code);
    }

    /**
     * Проверяет чтоб не повторялся баг SKILIKS-1567
     * 1. Неправильный конструктор в MS60
     * 2. Неправильная тема при написании re: для M75
     */
    public function testSubjectForMS60()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

        MailBoxService::copyMessageFromTemplateByCode($simulation, 'M75');


        $m75 = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M75'
        ]);

        $subject = MailBoxService::getSubjectForRepryEmail($m75);

        // check constructor
        $this->assertEquals('R14', $subject->constructor_number);

        // check template
        $this->assertEquals('MS60', $subject->letter_number);
    }
}

