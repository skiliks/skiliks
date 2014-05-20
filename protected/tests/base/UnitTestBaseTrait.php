<?php
trait UnitTestBaseTrait {

    /**
     * @var YumUser
     */
    public $user;

    /**
     * @var Invite
     */
    public $invite;

    /**
     * @var Simulation
     */
    public $simulation;

    /**
     * init asd@skiliks.com to $this->user
     */
    private function initTestUserAsd()
    {
        $profile = YumProfile::model()->findByAttributes(['email' => 'asd@skiliks.com']);
        $this->user = $profile->user;

        return $this->user;
    }

    /**
     * init standard invite and simulation
     * useful for 95% of tests
     *
     * @param string $scenarioSlug
     */
    private function standardSimulationStart($scenarioSlug = Scenario::TYPE_FULL)
    {
        $this->initTestUserAsd();
        $this->invite = new Invite();
        $this->invite->scenario = new Scenario();
        $this->invite->receiverUser = $this->user;
        $this->invite->scenario->slug = $scenarioSlug;
        $this->simulation = SimulationService::simulationStart($this->invite, Simulation::MODE_DEVELOPER_LABEL);
    }

    /**
     * Загрузка одиночного сообщения
     * @param int $id
     * @return array
     */
    public static function getMessage($id)
    {
        /* @var $email MailBox */
        $email = MailBox::model()->findByPk($id);
        if (null === $email) {
            return array();
        }

        // mark Readed
        $email->readed = 1;
        $email->save();

        $message = array(
            'id' => $email->id,
            'theme' => $email->getFormattedTheme(),
            'message' => $email->message,
            'sentAt' => GameTime::getDateTime($email->sent_at),
            'sender' => $email->sender_id,
            'receiver' => $email->receiver_id,
            'folder' => $email->group_id,
            'letterType'  => $email->letter_type
        );
        $message_id = $email->message_id;

        // Получим всех персонажей
        $characters = MailBoxService::getCharacters($email->simulation);

        // загрузим ка получателей
        $receivers = MailRecipient::model()->findAllByAttributes(['mail_id' => $id]);
        $receiversCollection = array();

        if (count($receivers) == 0)
            $receiversCollection[] = $characters[$message['receiver']];

        foreach ($receivers as $receiver) {
            $receiversCollection[] = $characters[$receiver->receiver_id];
        }
        $message['receiver'] = implode(',', $receiversCollection);

        // загрузим копии
        $copies = MailCopy::model()->findAllByAttributes(['mail_id' => $id]);
        $copiesCollection = array();
        foreach ($copies as $copy) {
            $copiesCollection[] = $characters[$copy->receiver_id];
        }
        $message['copies'] = implode(',', $copiesCollection);


        $message['sender'] = $characters[$message['sender']];

        // Собираем сообщение
        if ($message['message'] == '') {
            $message['message'] = MailBoxService::buildMessage($email->id);
        }

        $message['attachments'] = MailAttachmentsService::get($email);

        if (!empty($message_id)) {
            $reply = MailBox::model()->findByPk($message_id);
            $message['reply'] = $reply->message;
        }

        if ($email->group_id == MailBox::FOLDER_DRAFTS_ID && $email->constructor_code !== 'TXT') {
            $message['phrases'] = MailBoxService::getMessagePhrases($email);
            $message['phraseOrder'] = array_keys($message['phrases']);
        }

        return $message;
    }

    /**
     * Проверяет находится ли тема в списте тем, возвращённых PhoneService::getThemes()
     *
     * @param string[] $themes
     * @param string $name
     *
     * @return bool
     */
    public function findPhoneThemeByName($themes, $name) {
        foreach($themes as $theme){
            if($theme['themeTitle'] === $name){
                return true;
            }
        }
        return false;
    }

    /**
     * Проверяет находится ли тема в списте тем, возвращённых MailBoxService::getThemes()
     *
     * @param $themes
     * @param $name
     * @return bool
     */
    public function findMailThemeByName($themes, $name) {
        foreach($themes as $theme){
            if($theme === $name){
                return true;
            }
        }
        return false;
    }
}