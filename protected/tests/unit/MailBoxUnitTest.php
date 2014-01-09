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

        FlagsService::setFlag($simulation, 'F42', 1);
        FlagsService::setFlag($simulation, 'F33', 1);

        // 1. One recipient case {
        $Bobr = $simulation->game_type->getCharacter(['code' => 11]); // Бобр В.
        $BobrThemes = array_values(MailBoxService::getThemes($simulation, $Bobr->id, NULL,  NULL));

        // Check for no duplicates in theme list
        $this->assertEquals(count($BobrThemes), count(array_unique($BobrThemes)));

        $subjects = MailBoxService::getThemes($simulation, $Bobr->id, NULL, NULL);

        $this->assertEquals(3, count($subjects));
        $this->assertTrue(in_array('Бюджет производства прошлого года', $subjects));
        $this->assertTrue(in_array('Бюджет производства 2014: коррективы', $subjects));
        $this->assertTrue(in_array('Новая тема', $subjects));
        // 1. }

        // 2. + Several recipients case {
        $character1 = $Bobr->id;
        $character2 = $simulation->game_type->getCharacter(['code' => '26'])->getPrimaryKey();
        $character3 = $simulation->game_type->getCharacter(['code' => '24'])->getPrimaryKey();

        $subjects2 = MailBoxService::getThemes(
            $simulation,
            implode(',', [$character1, $character2, $character3]),
            NULL,
            NULL
        );
        $this->assertEquals(count($subjects2), 3);
        $this->assertTrue(in_array('Бюджет производства прошлого года', $subjects2));
        $this->assertTrue(in_array('Бюджет производства 2014: коррективы', $subjects2));
        $this->assertTrue(in_array('Новая тема', $subjects2));

        // 2. }
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

        $options->themeId = OutboxMailTheme::model()->findByAttributes([
            'scenario_id'     => $simulation->game_type->id,
            'character_to_id' => $character->primaryKey,
            'mail_prefix'     => 're'
        ])->theme_id;

        $hero = $simulation->game_type->getCharacter(['code' => Character::HERO_CODE]);

        $options->mailPrefix = 're';
        $options->setRecipientsArray($character->primaryKey);
        $options->senderId = $hero->id;
        $options->time = '11:00:00';
        $options->setLetterType('new');
        $options->groupId = MailBox::FOLDER_OUTBOX_ID;
        $options->simulation = $simulation;

        EventsManager::startEvent($simulation, 'M31');

        MailBoxService::copyMessageFromTemplateByCode($simulation, 'M31');

        /** @var MailBox $messageToReply */
        $messageToReply = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id, 
            'code' => 'M31'
        ]);

        $this->assertEquals('Re: Re: Срочно жду бюджет логистики', $messageToReply->getFormattedTheme('re'));
    }    
    
    /**
     * Проверяет темы для писем-перенаправлений:
     *
     * 1. Fwd для случайного письма
     *
     * 2. M61 - форвард для письма с одним Re:
     *
     * 3. M62 - форвард для письма с двумя Re:
     */
    public function testForward() 
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        // 1. random email case {
        /** @var MailBox $m8 */
        $m8 = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M8');
        $messageData = MailBoxService::getMessageData($m8, MailBoxService::ACTION_FORWARD);

        $mailOptions = new SendMailOptions($simulation);
        $mailOptions->setRecipientsArray($m8->sender_id);
        $mailOptions->themeId    = $m8->theme_id;
        $mailOptions->mailPrefix = 'fwd'.$m8->mail_prefix;
        $mailOptions->time       = '11:00';
        $mailOptions->messageId  = $m8->id;
        $m8_fwd = MailBoxService::sendMessagePro($mailOptions);
        $fwdMessageData = MailBoxService::getMessageData($m8_fwd, MailBoxService::ACTION_EDIT);

        $this->assertEquals($messageData['theme'],         'Fwd: '.$m8->getFormattedTheme(), 'M8 fwd theme text');
        $this->assertEquals($messageData['parentThemeId'], $m8->theme->id, 'M8 fwd parent theme Id');
        $this->assertEquals($messageData['themeId'],       $m8->theme_id, 'M8 fwd theme_id');
        $this->assertEquals($messageData['mailPrefix'],    'fwd'.$m8->mail_prefix, 'M8 fwd mailPrefix');

        $this->assertEquals($fwdMessageData['phrases']['previouseMessage'],
            $m8->message, 'M8 fwd previouseMessage');
        // 1. random email case }

        // case 2, M61, форвард для письма с одним Re: {
        /** @var MailBox $emailM61 */
        $emailM61 = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M61');
        $M61data = MailBoxService::getMessageData($emailM61, MailBoxService::ACTION_FORWARD);

        $mailOptions = new SendMailOptions($simulation);
        $mailOptions->setRecipientsArray($emailM61->sender_id);
        $mailOptions->themeId    = $emailM61->theme_id;
        $mailOptions->mailPrefix = 'fwd'.$emailM61->mail_prefix;
        $mailOptions->time       = '11:01';
        $mailOptions->messageId  = $emailM61->id;
        MailBoxService::sendMessagePro($mailOptions);

        $this->assertEquals(1, substr_count($M61data['theme'],'Re:'));

        $this->assertEquals($M61data['theme'],         'Fwd: '.$emailM61->getFormattedTheme(), 'M8 theme text');
        $this->assertEquals($M61data['parentThemeId'], $emailM61->theme->id, 'M8 parent theme Id');
        $this->assertEquals($M61data['themeId'],       $emailM61->theme_id, 'M8 theme_id');
        $this->assertEquals($M61data['mailPrefix'],    'fwd'.$emailM61->mail_prefix, 'M8 mailPrefix');
        // case 2, M61 }

        // case 3, M62, форвард для письма с двумя Re: {
        /** @var MailBox $emailM62 */
        $emailM62 = MailBoxService::copyMessageFromTemplateByCode($simulation, 'M62');
        $M62data = MailBoxService::getMessageData($emailM62, MailBoxService::ACTION_FORWARD);

        $mailOptions = new SendMailOptions($simulation);
        $mailOptions->setRecipientsArray($emailM62->sender_id);
        $mailOptions->themeId    = $emailM62->theme_id;
        $mailOptions->mailPrefix = 'fwd'.$emailM62->mail_prefix;
        $mailOptions->time       = '11:02';
        $mailOptions->messageId  = $emailM62->id;
        MailBoxService::sendMessagePro($mailOptions);

        $this->assertEquals(2, substr_count($M62data['theme'],'Re:'));

        $this->assertEquals($M62data['theme'],         'Fwd: '.$emailM62->getFormattedTheme(), 'M8 theme text');
        $this->assertEquals($M62data['parentThemeId'], $emailM62->theme->id, 'M8 parent theme Id');
        $this->assertEquals($M62data['themeId'],       $emailM62->theme_id, 'M8 theme_id');
        $this->assertEquals($M62data['mailPrefix'],    'fwd'.$emailM62->mail_prefix, 'M8 mailPrefix');
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

        $Denejnaja = $simulation->game_type->getCharacter(['fio'=>'Денежная Р.Р.']);

        /** @var OutboxMailTheme $theme */
        $OutboxTheme = $simulation->game_type->getOutboxMailTheme([
            'character_to_id'  => $Denejnaja->id,
            'mail_prefix'      => null,
            'mail_code'        => 'MS35'
        ]);

        $this->assertNotNull($OutboxTheme);

        $mail_phrases = MailPhrase::model()->findAllByAttributes([
            'constructor_id' => $OutboxTheme->mail_constructor_id
        ]);
        $data= [];
        
        foreach($mail_phrases as $phrase){
            $data[$phrase->id] = ['name'=>$phrase->name, 'column_number'=>$phrase->column_number];
        }
        
        $phrases = MailBoxService::getPhrases($simulation, $OutboxTheme->theme->id, $Denejnaja->id, null);
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

        $Trutnev = $simulation->game_type->getCharacter(['fio'=>'Трутнев С.']);

        $OutboxMailTheme = $simulation->game_type->getOutboxMailTheme([
            'character_to_id' => $Trutnev->id,
            'mail_prefix'     => 'fwd',
            'mail_code'       => 'MS42'
        ]);

        $constructor = $simulation->game_type->getMailConstructor(['code' => 'R6']);
        $mail_phrases = MailPhrase::model()->findAllByAttributes(['constructor_id'=>$constructor->getPrimaryKey()]);
        $data= [];

        foreach($mail_phrases as $phrase){
            $data[$phrase->id] = ['name'=>$phrase->name, 'column_number'=>$phrase->column_number];
        }

        $phrases = MailBoxService::getPhrases($simulation, $OutboxMailTheme->theme->id, $Trutnev->id, 'fwd');

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
        $Trutnev = $simulation->game_type->getCharacter(['code' =>'3']);


        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray($Trutnev->id);
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = $emailFromSysadmin->id;
        $sendMailOptions->time       = '09:01';
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->mailPrefix = 'fwd';
        $sendMailOptions->themeId   = $emailFromSysadmin->theme->id;

        $ms_27 = MailBoxService::sendMessagePro($sendMailOptions);
        // MS27 }

        $ms_27->refresh();

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

        $mailHero = $simulation->game_type->getCharacter(['code' => Character::HERO_CODE]);

        /** @var MailBox $m75 */
        $m75 = MailBox::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'code'   => 'M75'
        ]);

        /** @var OutboxMailTheme $outboxTheme */
        $outboxTheme = OutboxMailTheme::model()->findByAttributes([
            'scenario_id'     => $simulation->game_type->id,
            'theme_id'        => $m75->theme_id,
            'character_to_id' => $m75->sender_id,
            'mail_prefix'     => 're'
        ]);

        // check constructor
        $this->assertEquals('R14', $outboxTheme->mailConstructor->code);

        // check template
        $this->assertEquals('MS60', $outboxTheme->mail_code);

        $data = MailBoxService::getPhrases($simulation, $m75->theme_id, $m75->sender_id, 're');
        $this->assertEquals('R14', $data['constructorCode']);
    }

    /**
     * Проверяет параметры случайно выбранного нового отправленного письма.
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
            'limit'     => 1,
            'order'     => 'rand()',
            'condition' => ' mail_prefix is NULL ',
        ]);

        // Some random $OutboxMailTheme
        /** @var OutboxMailTheme $OutboxMailTheme */
        $OutboxMailTheme = $simulation->game_type->getOutboxMailTheme($criteria);

        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray(implode(',', $recipients));
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = 0;
        $sendMailOptions->time       = date('H:i', rand(32400, 64800));
        $sendMailOptions->copies     = implode(',', $copies);
        $sendMailOptions->phrases    = '';
        $sendMailOptions->themeId    = $OutboxMailTheme->theme->id;

        $sentMessage = MailBoxService::sendMessagePro($sendMailOptions);
        $sentMessage->refresh();
        $foundMessage = UnitTestBaseTrait::getMessage($sentMessage->id);

        $sentMessage->refresh();

        $this->assertEquals(1, $sentMessage->readed);
        $this->assertArrayHasKey('id', $foundMessage);
        $this->assertSame($sentMessage->id, $foundMessage['id']);
        $this->assertSame($OutboxMailTheme->theme->text, $foundMessage['theme']);
        $this->assertEquals(count($recipients), count(explode(',', $foundMessage['receiver'])));
    }

    /**
     * В принципе дублирует testGetMessage() в этом же тесте
     * Проверяет параметры случайно выбранного нового отправленного письма.
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

        $baseCriteria = new CDbCriteria([
            'limit' => 1,
            'order' => 'rand()',
        ]);

        $recipients = $getRandomCharacters();
        $copies = $getRandomCharacters(0, 5);


        $OutboxMailTheme = $simulation->game_type->getOutboxMailTheme(new CDbCriteria([
            'limit' => 1,
            'order' => 'rand()',
            'condition' => ' mail_prefix is NULL ',
        ]));

        $condition = clone $baseCriteria;
        $condition->addColumnCondition(['sim_id' => $simulation->id]);

        $condition = clone $baseCriteria;
        $condition->limit = rand(0, 50);
        $condition->addColumnCondition(['scenario_id' => $simulation->game_type->id]);
        $phrases = $toList(MailPhrase::model()->findAll($condition));

        $condition = clone $baseCriteria;
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
        $sendMailOptions->themeId    = $OutboxMailTheme->theme->id;
        $sendMailOptions->themeId    = $OutboxMailTheme->theme->id;
        $sendMailOptions->phrases    = implode(',', $phrases);

        $sentMessage = MailBoxService::sendMessagePro($sendMailOptions);

        $this->assertInstanceOf('MailBox', $sentMessage);
        $this->assertGreaterThan($someEmail->id, $sentMessage->id);
        $this->assertSame($OutboxMailTheme->theme->id, $sentMessage->theme_id);
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
     * Проверяет что игра правильно перезаписывает данные письма при сохранениии черновиков
     */
    public function testUpdateMessage() {

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_DEVELOPER_LABEL);

        // 1. Сохранить MS20 {
        $recipients = [];
        $recipients[] = $simulation->game_type->getCharacter(['fio'=>'Денежная Р.Р.'])->id;
        $recipients[] = $simulation->game_type->getCharacter(['fio'=>'Крутько М.'])->id;

        $copies = [];
        $copies[] = $simulation->game_type->getCharacter(['fio'=>'Железный С.'])->id;

        /** @var OutboxMailTheme $OutboxMailTheme */
        $OutboxMailTheme = $simulation->game_type->getOutboxMailTheme([
            'character_to_id' => $recipients[0],
            'mail_prefix'     => null,
            'mail_code'       => 'MS20'
        ]);

        $constructor = $simulation->game_type->getMailConstructor([
            'code' => $OutboxMailTheme->mailConstructor->code
        ]);

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
        $sendMailOptions->themeId    = $OutboxMailTheme->theme->id;
        $sendMailOptions->id         = null;
        $sendMailOptions->setLetterType('new');

        $email = MailBoxService::saveDraft($sendMailOptions);

        unset($recipients);
        unset($OutboxMailTheme);
        unset($phrases);
        unset($constructor);
        unset($sendMailOptions);
        unset($attach);
        $recipients = [];
        // 1. MS20 }

        // 2. Сохранить MS20 из п.1 как не MS. {
        $recipients[] = $simulation->game_type->getCharacter(['fio'=>'Босс В.С.'])->id;

        /** @var Theme $theme */
        $theme = $simulation->game_type->getTheme([
            'text' => 'Индексация ЗП',
        ]);

        $constructor = $simulation->game_type->getMailConstructor([
            'code' => 'B1'
        ]);
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
        $sendMailOptions->themeId    = $theme->id;
        $sendMailOptions->id         = $email->id;
        $sendMailOptions->setLetterType('new');
        $draft = MailBoxService::saveDraft($sendMailOptions);

        $mail = UnitTestBaseTrait::getMessage($email->id);
        // 2. Не MS. }

        $this->assertNotEmpty($mail);

        $this->assertEquals('Индексация ЗП', $mail['theme']);
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
        $OutboxMailTheme = $simulation->game_type->getOutboxMailTheme(['mail_code' => 'MS20', 'mail_prefix' => null]);

        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray(implode(',', [$person->id]));
        $sendMailOptions->simulation   = $simulation;
        $sendMailOptions->messageId    = 0;
        $sendMailOptions->time         = '10:00';
        $sendMailOptions->copies       = [];
        $sendMailOptions->phrases      = [];
        $sendMailOptions->fileId       = 0;
        $sendMailOptions->themeId      = $OutboxMailTheme->theme->id;
        $sendMailOptions->setLetterType('new');

        $message1 = MailBoxService::sendMessagePro($sendMailOptions);

        $person = $simulation->game_type->getCharacter(['code' => 11]);
        $OutboxMailTheme = $simulation->game_type->getOutboxMailTheme(['mail_code' => 'MS28', 'mail_prefix' => null]);

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
        $sendMailOptions->themeId      = $OutboxMailTheme->theme->id;
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

    /**
     * Проверяет, что для LITE версии нельзя никому написать письмо
     */
    public function testMailCharactersToListForLiteSim()
    {
        $this->standardSimulationStart(Scenario::TYPE_LITE);

        $list = SimulationService::getCharactersList($this->simulation);

        $isFail = false;

        foreach ($list as $character) {
            if (1 == $character['has_mail_theme']) {
                var_dump($character);
                $isFail = ture;
            }
        }

        $this->assertFalse($isFail, 'В lite версии не должно быть адресатов, при написании нового письма.');
    }

    /**
     * Проверяет, что для FULL версии есть 23 доступные адресата
     */
    public function testMailCharactersToListForFullSim()
    {
        $this->standardSimulationStart();

        $list = SimulationService::getCharactersList($this->simulation);

        $count = 0;

        foreach ($list as $character) {
            if (1 == $character['has_mail_theme']) {
                $count++;
            }
        }

        $this->assertEquals(23, $count);
    }
}

