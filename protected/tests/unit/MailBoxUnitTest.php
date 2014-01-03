<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 06.02.13
 * Time: 12:18
 * To change this template use File | Settings | File Templates.
 */
class MailBoxUnitTest extends CDbTestCase
{
    use UnitTestBaseTrait;
    use UnitLoggingTrait;

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
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        LibSendMs::sendMs($simulation, 'MS40');

        FlagsService::setFlag($simulation, 'F30', 1);

        EventsManager::startEvent($simulation,'M31');
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
        // get letters from folders to check them }

        $this->assertEquals(6, count($folderInbox));
        $this->assertEquals(3, count($folderOutbox));
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
        $this->assertEquals('Re: срочно! Отчетность', $sent_letters[2]['subject']);

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
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);
        //
        $bossSubjects = array_values(MailBoxService::getThemes($simulation, '6', null, null));
        // Check for no duplicates in theme list
        $this->assertEquals(count($bossSubjects), count(array_unique($bossSubjects)));
        // one recipient case :
        /** @var $scenario Scenario */
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $characterId = $scenario->getCharacter(['code' => '11'])->getPrimaryKey();

        FlagsService::setFlag($simulation, 'F42', 1);
        FlagsService::setFlag($simulation, 'F33', 1);

        $subjects = MailBoxService::getThemes($simulation, $characterId, null, null);

        $this->assertEquals(3, count($subjects));
        $this->assertTrue(in_array('Бюджет производства прошлого года', $subjects));
        $this->assertTrue(in_array('Бюджет производства 2014: коррективы', $subjects));
        $this->assertTrue(in_array('Новая тема', $subjects));

        // several recipients case :
        $character1 = $scenario->getCharacter(['code' => '11'])->getPrimaryKey();
        $character2 = $scenario->getCharacter(['code' => '26'])->getPrimaryKey();
        $character3 = $scenario->getCharacter(['code' => '24'])->getPrimaryKey();
        $subjects2 = MailBoxService::getThemes($simulation, join(',', [$character1, $character2, $character3]), null, null);

        $this->assertEquals(count($subjects2), 3);
        $this->assertTrue(in_array('Бюджет производства прошлого года', $subjects2));
        $this->assertTrue(in_array('Бюджет производства 2014: коррективы', $subjects2));
        $this->assertTrue(in_array('Новая тема', $subjects2));
        
    }

    /**
     * 1. Проверяет тему письма, которое является ответом на M31.
     * Тема нового письма должна быть 'Re: Re: Срочно жду бюджет логистики'
     */
    public function testSubjectsForReReCase() 
    {
        //$this->markTestSkipped();

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        $character = $simulation->game_type->getCharacter(['code' => 9]);

        $options = new SendMailOptions($simulation);
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

        EventsManager::startEvent($simulation, 'M31');

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
     * 1. Проверяет что дляписьма M8 из списка писем с темой "ххх",
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
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // random email case{
        $randomFirstEmail = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M8');
        $resultData = MailBoxService::getMessageData($randomFirstEmail, MailBox::TYPE_FORWARD);

        $fwdSubject = CommunicationTheme::model()->findByAttributes([
            'character_id' => null,
            'theme_usage'  => CommunicationTheme::USAGE_OUTBOX,
            'text'         => $randomFirstEmail->subject_obj->text,
            'mail_prefix'  => 'fwd',
            'scenario_id' => $simulation->scenario_id,
        ]);

        $this->assertEquals($resultData['subject'], 'Fwd: '.$randomFirstEmail->subject_obj->text, 'M8 subject tetx');
        $this->assertEquals($resultData['parentSubjectId'], $randomFirstEmail->subject_obj->id, 'M8 parentSubjectId');
        $this->assertEquals($resultData['subjectId'], $fwdSubject->id, 'M8 subjectId');

        $ITlead = Character::model()->findByAttributes([
            'scenario_id' => $simulation->scenario_id,
            'fio'         => 'Железный С.',
        ]);

        $fwdSubject = CommunicationTheme::model()->findAllByAttributes([
            'character_id' => $ITlead->id,
            'theme_usage'  => CommunicationTheme::USAGE_OUTBOX,
            'text'         => $randomFirstEmail->subject_obj->text,
            'mail_prefix'  => 'fwd',
            'scenario_id' => $simulation->scenario_id,
        ]);
        $this->assertEquals(1, count($fwdSubject), 'M8 wrong recipient mail subject');
        // random email case }

        // case 2, M61 {      
        $emailM61 = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M61');
        $resultDataM61 = MailBoxService::getMessageData($emailM61, MailBox::TYPE_FORWARD);

        $this->assertEquals($resultDataM61['subject'], 'Fwd: Re: '.$emailM61->subject_obj->text, 'M61');
        $this->assertEquals($resultDataM61['parentSubjectId'], $emailM61->subject_obj->id, 'M61');

        $subject = MailBoxService::getThemes($simulation, '18', $emailM61->subject_id);
        // case 2, M61 }

        // case 3, M62 {
        $emailM62 = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M62');
        $resultDataM62 = MailBoxService::getMessageData($emailM62, MailBox::TYPE_FORWARD);
        
        $this->assertEquals($resultDataM62['subject'], 'Fwd: Re: Re: '.$emailM62->subject_obj->text, 'M62');
        $this->assertEquals($resultDataM62['parentSubjectId'], $emailM62->subject_obj->id, 'M62');
        
        $subject = MailBoxService::getThemes($simulation, '18', $emailM62->subject_id);
        // case 3, M62 }
    }

    /**
     * 1. Проверяет что для нового письма Денежной на тему "'Сводный бюджет" возвращается непустой набор
     *    правильных фраз
     */
    public function testGetPhrases()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $ch = $simulation->game_type->getCharacter(['fio'=>'Денежная Р.Р.']);
        $theme = $simulation->game_type->getCommunicationTheme(['character_id'=>$ch->id, 'text'=>'Сводный бюджет', 'letter_number'=>'MS35']);
        $this->assertNotNull($theme);
        $constructor = MailConstructor::model()->findByAttributes(['code' => 'R1', 'scenario_id' => $simulation->game_type->getPrimaryKey()]);
        $mail_phrases = MailPhrase::model()->findAllByAttributes(['constructor_id' => $constructor->getPrimaryKey()]);
        $data= [];
        
        foreach($mail_phrases as $phrase){
            $data[$phrase->id] = ['name'=>$phrase->name, 'column_number'=>$phrase->column_number];
        }
        
        $phrases = MailBoxService::getPhrases($theme->id, 0, $simulation);
        $this->assertNotEmpty($data);
        $this->assertEquals($data, $phrases['data']);
        $this->assertEquals(count($data), count($phrases['data']));

        foreach ($phrases['data'] as $phrase) {
            $this->assertTrue(in_array($phrase, $data));
        }
    }

    /**
     * Checks that punctuation signs available
     */
    public function testPunctuationSignsExist()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $allSings = MailBoxService::getSigns($simulation);
        $this->assertCount(6, $allSings);
    }

    /**
     * Проверяет что для письма Трутнев С. на тему "FWD:форма по задаче от логистики, срочно!" возвращается непустой набор
     * правильных фраз и равен R6
     *
     */
    public function testGetPhrasesFWD()
    {
        //$this->markTestSkipped();
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $ch = $simulation->game_type->getCharacter(['fio'=>'Трутнев С.']);
        $theme = $simulation->game_type->getCommunicationTheme(['character_id'=>$ch->id,'text'=>'форма по задаче от логистики, срочно!', 'letter_number'=>'MS42']);
        $constructor = $simulation->game_type->getMailConstructor(['code' => 'R6']);
        $mail_phrases = MailPhrase::model()->findAllByAttributes(['constructor_id'=>$constructor->getPrimaryKey()]);
        $data= [];

        foreach($mail_phrases as $phrase){
            $data[$phrase->id] = ['name'=>$phrase->name, 'column_number'=>$phrase->column_number];
        }

        $phrases = MailBoxService::getPhrases(0, $theme->id, $simulation);

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
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


        // init conts
        // get all replics that change score for behaviour '4124'
        $criteria = new CDbCriteria();
        $criteria->addInCondition('excel_id', [332, 336]);
        $replicsFor_4124 = $simulation->game_type->getReplicas($criteria);

        $count_0 = 0;
        $count_1 = 0;

        // get 4124
        $pointFor_4124 = $simulation->game_type->getHeroBehaviour(['code' => '4124']);

        // init dialog logs
        foreach($replicsFor_4124 as $dialogEntity) {
            $dialogsPoint = ReplicaPoint::model()->find('dialog_id = :dialog_id AND point_id = :point_id',[
                'dialog_id' => $dialogEntity->id,
                'point_id'  => $pointFor_4124->id
            ]);

            LogHelper::setDialogPoint( $dialogEntity->id, $simulation->id, $dialogsPoint);

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
        $subject = $simulation->game_type->getCommunicationTheme(
            ['text' => '!проблема с сервером!', 'letter_number' => 'MS27']
        );

        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray($simulation->game_type->getCharacter(['code' =>'3'])->getPrimaryKey()); // Трутнев
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
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);


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

    /**
     *
     */
    public function testGetMessage()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $getRandomCharacters = function($max = 3) use ($simulation) {
            $codes = range(1, 20);
            shuffle($codes);
            $codes = array_slice($codes, 0, rand(1, $max));

            return array_map(function(Character $character) {
                return $character->getPrimaryKey();
            }, $simulation->game_type->getCharacters(['code' => $codes]));
        };

        $recipients = $getRandomCharacters();
        $copies = $getRandomCharacters();

        $criteria = new CDbCriteria([
            'limit' => 1,
            'order' => 'rand()'
        ]);

        // Some random subject
        $subject = $simulation->game_type->getCommunicationTheme($criteria);

        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray(implode(',', $recipients));
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = 0;
        $sendMailOptions->time       = date('H:i', rand(32400, 64800));
        $sendMailOptions->copies     = implode(',', $copies);
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;

        $sentMessage = MailBoxService::sendMessagePro($sendMailOptions);
        $foundMessage = UnitTestBaseTrait::getMessage($sentMessage->id);

        $sentMessage->refresh();

        $this->assertEquals(1, $sentMessage->readed);
        $this->assertArrayHasKey('id', $foundMessage);
        $this->assertSame($sentMessage->id, $foundMessage['id']);
        $this->assertSame($subject->text, $foundMessage['subject']);
        $this->assertEquals(count($recipients), count(explode(',', $foundMessage['receiver'])));
    }

    /**
     *
     */
    public function testSendMessagePro()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $toList = function($modelList) {
            return array_map(function(CActiveRecord $model) {
                return $model->getPrimaryKey();
            }, $modelList);
        };

        $getRandomCharacters = function($min = 1, $max = 3) use ($simulation, $toList) {
            $codes = range(1, 20);
            shuffle($codes);
            $codes = array_slice($codes, 0, rand($min, $max));

            return $toList($simulation->game_type->getCharacters(['code' => $codes]));
        };

        $criteria = new CDbCriteria([
            'limit' => 1,
            'order' => 'rand()'
        ]);


        $recipients = $getRandomCharacters();
        $copies = $getRandomCharacters(0, 5);

        $condition = clone $criteria;
        $subject = $simulation->game_type->getCommunicationTheme($condition);

        $condition = clone $criteria;
        $condition->addColumnCondition(['sim_id' => $simulation->id]);
        $doc = MyDocument::model()->find($condition);

        $condition = clone $criteria;
        $condition->limit = rand(0, 50);
        $condition->addColumnCondition(['scenario_id' => $simulation->game_type->id]);
        $phrases = $toList(MailPhrase::model()->findAll($condition));

        $condition = clone $criteria;
        $condition->addColumnCondition(['sim_id' => $simulation->id]);
        $someEmail = MailBox::model()->find($condition);

        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray(implode(',', $recipients));
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = $someEmail->id;
        $sendMailOptions->setLetterType(MailBox::TYPE_REPLY);
        $sendMailOptions->time       = date('H:i', rand(32400, 64800));
        $sendMailOptions->copies     = implode(',', $copies);
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->fileId = $doc->id;
        $sendMailOptions->phrases = implode(',', $phrases);

        $sentMessage = MailBoxService::sendMessagePro($sendMailOptions);

        $this->assertInstanceOf('MailBox', $sentMessage);
        $this->assertGreaterThan($someEmail->id, $sentMessage->id);
        $this->assertSame($subject->id, $sentMessage->subject_id);
    }

    /**
     *
     */
    public function testMessageBoxCounter()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        MailBoxService::copyMessageFromTemplateByCode($simulation, 'M1');
        MailBoxService::copyMessageFromTemplateByCode($simulation, 'M2');
        MailBoxService::copyMessageFromTemplateByCode($simulation, 'M3');
        MailBoxService::copyMessageFromTemplateByCode($simulation, 'M4');

        $mail = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M5');
        MailBoxService::moveToFolder($mail, MailBox::FOLDER_TRASH_ID);

        $unread = MailBoxService::getFoldersUnreadCount($simulation);

        $this->assertEquals(4, $unread[MailBox::FOLDER_INBOX_ID]);
        $this->assertEquals(0, $unread[MailBox::FOLDER_DRAFTS_ID]);
        $this->assertEquals(0, $unread[MailBox::FOLDER_OUTBOX_ID]);
        $this->assertEquals(1, $unread[MailBox::FOLDER_TRASH_ID]);
    }

    /**
     *
     */
    public function testUpdateMessage() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $recipients = [];
        $recipients[] = $simulation->game_type->getCharacter(['fio'=>'Денежная Р.Р.'])->id;
        $recipients[] = $simulation->game_type->getCharacter(['fio'=>'Крутько М.'])->id;

        $copies = [];
        $copies[] = $simulation->game_type->getCharacter(['fio'=>'Железный С.'])->id;

        $subject = $simulation->game_type->getCommunicationTheme(['character_id'=>$recipients[0], 'mail'=>1, 'letter_number'=>"MS20"]);

        /* @var $subject CommunicationTheme */
        $constructor = $simulation->game_type->getMailConstructor(['code' => $subject->constructor_number]);

        $phrases = [];
        $phrases[] = $simulation->game_type->getMailPhrase(['constructor_id' => $constructor->id, 'name' => 'аналитический отдел'])->id;
        $phrases[] = $simulation->game_type->getMailPhrase(['constructor_id' => $constructor->id, 'name' => 'ближайшие дни'])->id;

        $attach = MyDocument::model()->findByAttributes(['sim_id'=>$simulation->id, 'fileName'=>"Бюджет производства_2013_утв.xls"]);

        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray(implode(',', $recipients));
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = 0;
        $sendMailOptions->time = '10:00:00';
        $sendMailOptions->copies     = implode(',', $copies);
        $sendMailOptions->phrases    = implode(',', $phrases);
        $sendMailOptions->fileId     = $attach->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->id         = null;
        $sendMailOptions->setLetterType('new');

        $email = MailBoxService::saveDraft($sendMailOptions);

        unset($recipients);
        unset($subject);
        unset($phrases);
        unset($constructor);
        unset($sendMailOptions);
        unset($attach);
        $recipients = [];
        $recipients[] = $simulation->game_type->getCharacter(['fio'=>'Босс В.С.'])->id;

        $subject = $simulation->game_type->getCommunicationTheme(['character_id'=>$recipients[0], 'mail'=> 1, 'text'=>'Индексация ЗП']);

        /* @var $subject CommunicationTheme */
        $constructor = $simulation->game_type->getMailConstructor(['code' => $subject->constructor_number]);
        $phrases = [];

        $phrases[] = $simulation->game_type->getMailPhrase(['constructor_id'=>$constructor->id, 'name' => 'спасибо'])->id;
        $phrases[] = $simulation->game_type->getMailPhrase(['constructor_id'=>$constructor->id, 'name' => 'сделаю'])->id;
        $phrases[] = $simulation->game_type->getMailPhrase(['constructor_id'=>$constructor->id, 'name' => 'хорошо'])->id;

        $doc = $simulation->game_type->getDocumentTemplate(['code'=>"D5"]);
        $attach = MyDocument::model()->findByAttributes(['sim_id'=>$simulation->id, 'template_id'=>$doc->id]);

        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray(implode(',', $recipients));
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = 0;
        $sendMailOptions->time = '10:20:00';
        $sendMailOptions->copies     = implode(',', $copies);
        $sendMailOptions->phrases    = implode(',', $phrases);
        $sendMailOptions->fileId     = $attach->id;
        $sendMailOptions->subject_id = $subject->id;
        $sendMailOptions->id         = $email->id;
        $sendMailOptions->setLetterType('new');
        $draft = MailBoxService::saveDraft($sendMailOptions);

        $mail = UnitTestBaseTrait::getMessage($email->id);

        $this->assertNotEmpty($mail);

        $this->assertEquals('Индексация ЗП', $mail['subject']);
        $this->assertEquals('спасибо сделаю хорошо', $mail['message']);
        $this->assertEquals($simulation->game_type->scenario_config->game_date_data.' 10:20', $mail['sentAt']);
        $this->assertEquals("Босс В.С. <boss@skiliks.com>", $mail['receiver']);
        $this->assertEquals('2', $mail['folder']);
        $this->assertEquals('Железный С. <zhelezniy.so@skiliks.com>', $mail['copies']);
        $this->assertEquals('Презентация_ ГД_2013_итог.pptx', $mail['attachments']['name']);
        $this->assertEquals($email->id, $mail['id']);
        $this->assertEquals($draft->id, $email->id);
    }

    public function testParentActivityCompletedOnSend()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $person = $simulation->game_type->getCharacter(['code' => 2]);
        $theme = $simulation->game_type->getCommunicationTheme(['letter_number' => 'MS20']);

        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray(implode(',', [$person->id]));
        $sendMailOptions->simulation   = $simulation;
        $sendMailOptions->messageId    = 0;
        $sendMailOptions->time         = '10:00';
        $sendMailOptions->copies       = [];
        $sendMailOptions->phrases      = [];
        $sendMailOptions->fileId       = 0;
        $sendMailOptions->subject_id   = $theme->id;
        $sendMailOptions->setLetterType('new');

        $message1 = MailBoxService::sendMessagePro($sendMailOptions);

        $person = $simulation->game_type->getCharacter(['code' => 11]);
        $theme = $simulation->game_type->getCommunicationTheme(['letter_number' => 'MS28']);
        $docTemplate = $simulation->game_type->getDocumentTemplate(['code' => 'D8']);
        $document = MyDocument::model()->findByAttributes(['sim_id' => $simulation->id, 'template_id' => $docTemplate->id]);

        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray(implode(',', [$person->id]));
        $sendMailOptions->simulation   = $simulation;
        $sendMailOptions->messageId    = 0;
        $sendMailOptions->time         = '11:00';
        $sendMailOptions->copies       = [];
        $sendMailOptions->phrases      = [];
        $sendMailOptions->fileId       = $document->id;
        $sendMailOptions->subject_id   = $theme->id;
        $sendMailOptions->setLetterType('new');

        $message2 = MailBoxService::saveDraft($sendMailOptions);

        $logs = [
            [1, 1, 'activated', 32400, 'window_uid' => 1],
            [1, 1, 'deactivated', 32460, 'window_uid' => 1],
            [10, 11, 'activated', 32460, 'window_uid' => 2],
            [10, 11, 'deactivated', 32520, 'window_uid' => 2],
            [10, 13, 'activated', 32520, 'window_uid' => 3],
            [10, 13, 'deactivated', 32580, 'window_uid' => 3, ['mailId' => $message1->primaryKey]]
        ];
        EventsManager::processLogs($simulation, $logs);

        $this->assertCount(1, $simulation->completed_parent_activities);

        MailBoxService::sendDraft($simulation, $message2);

        $logs = [
            [10, 11, 'activated', 32580, 'window_uid' => 4, ['mailId' => $message2->primaryKey]],
            [10, 11, 'deactivated', 32640, 'window_uid' => 4, ['mailId' => $message2->primaryKey]],
            [10, 13, 'activated', 32640, 'window_uid' => 5, ['mailId' => $message2->primaryKey]],
            [10, 13, 'deactivated', 32700, 'window_uid' => 5, ['mailId' => $message2->primaryKey]],
            [10, 11, 'activated', 32700, 'window_uid' => 6, ['mailId' => $message2->primaryKey]],
            [10, 11, 'deactivated', 32730, 'window_uid' => 6, ['mailId' => $message2->primaryKey]],
            [10, 11, 'activated', 32800, 'window_uid' => 7],
            [10, 11, 'deactivated', 32850, 'window_uid' => 7]
        ];
        EventsManager::processLogs($simulation, $logs);

        $simulation->refresh();
        $this->assertCount(2, $simulation->completed_parent_activities);
    }

    public function testFlagSwitchEmpty(){
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        $templates = $simulation->game_type->getMailTemplates([]);

        foreach($templates as $template){
            /* @var MailTemplate $template */
            if($template->flag_to_switch !== null){
                $flag = Flag::model()->findByAttributes(['code'=>$template->flag_to_switch]);
                $this->assertNotNull($flag);
            }
        }
    }

    /**
     * Гавная цель теста проверить что при отправке письма "из DEV панели" игрок получит AssessmentPoints.
     * Под отправкой издев панели понимается использование метода EventsManager::startEvent().
     */
    public function testSendEmailInDevMode()
    {
        $this->standardSimulationStart();

        // logging "activate MainScreen"
        // чтоб правильно залогировалось EventsManager::startEvent
        // надо чтобы перед отправкой письма было залогировано открытое окно

        $eventsManager = new EventsManager();
        $logs = [];
        $logs[] = [1, 1, 'activated', 32400, 'window_uid' => 1];
        $eventsManager->processLogs($this->simulation, $logs);

        EventsManager::startEvent($this->simulation, 'MS103', 0, 32410);

        $universalLogs = UniversalLog::model()->findAllByAttributes(['sim_id' => $this->simulation->id]);

        // 3 лога, открытие/закрытия MailMain, отрытие 1 MailNew
        $this->assertEquals(3, count($universalLogs));

        SimulationService::simulationStop($this->simulation);

        $activityActionsLogs = LogActivityAction::model()->findAllByAttributes(['sim_id' => $this->simulation->id]);
        $this->assertEquals(3, count($activityActionsLogs));

        // проверка что симстоп не удалил какие-то $UniversalLogs
        $UniversalLogs = UniversalLog::model()->findAllByAttributes(['sim_id' => $this->simulation->id]);
        $this->assertEquals(3, count($UniversalLogs));

        $assessmentPoints = AssessmentPoint::model()->findAllByAttributes(['sim_id' => $this->simulation->id]);
        $this->assertEquals(3, count($assessmentPoints));

        $mail_id = null;

        foreach ($assessmentPoints as $assessmentPoint) {
            if (null === $mail_id) {
                $mail_id = $assessmentPoint->mail_id;
            } else {
                $this->assertEquals($mail_id, $assessmentPoint->mail_id);
            }
        }
    }
}

