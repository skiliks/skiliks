<?php
class LibSendMs
{
     /**
     * @param Simulation $simulation
     * @return MailBoxModel
     */
    public static function sendMs27($simulation)
    {
        $emailFromSysadmin = MailBoxModel::model()
            ->find('sim_id = :sim_id AND code = \'M8\'', ['sim_id' => $simulation->id ]);

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

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBoxModel
     */
    public static function sendMs28($simulation) {
        $subject = CommunicationTheme::model()->find(
            'text = :text AND letter_number = :letter_number',[
            'text'          => 'Бюджет производства прошлого года',
            'letter_number' => 'MS28'
        ]);

        $attachment = MyDocumentsModel::model()->find(
            'sim_id = :sim_id AND fileName = \'Бюджет производства_01_итог.xlsx\'',[
            'sim_id' => $simulation->id
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('11'); // Трутнев
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:02';
        $sendMailOptions->messageId  = 0;
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->fileId     = $attachment->id;
        $sendMailOptions->subject_id = $subject->id;

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBoxModel
     */
    public static function sendMs29($simulation)
    {
        $subject = CommunicationTheme::model()->find(
            'text = :text AND letter_number = :letter_number',[
            'text'          => 'задача: бюджет производства прошлого года',
            'letter_number' => 'MS29'
        ]);

        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray('3'); // Трутнев
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->time       = '09:03';
        $sendMailOptions->messageId  = 0;
        $sendMailOptions->copies     = '';
        $sendMailOptions->phrases    = '';
        $sendMailOptions->subject_id = $subject->id;

        return MailBoxService::sendMessagePro($sendMailOptions);
    }

    /**
     * @param Simulation $simulation
     * @return MailBoxModel
     */
    public static function sendMs30($simulation)
    {
        $senderId = Characters::model()->findByAttributes(['code' => Characters::HERO_ID])->primaryKey;
        $receiverId = Characters::model()->findByAttributes(['code' => '12'])->primaryKey;

        $msgParams = [
            'simId' => $simulation->id,
            'subject_id' => CommunicationTheme::model()->findByAttributes([
                'code'=>55,
                'character_id' => $receiverId,
                'mail_prefix'=>null
            ])->primaryKey,
            'message_id' => 0,
            'receivers' => $receiverId,
            'group' => MailBoxModel::OUTBOX_FOLDER_ID,
            'sender' => $senderId,
            'time' => '11:00',
            'letterType' => null
        ];

        return MailBoxService::sendMessage($msgParams);
    }

    /**
     * @param Simulation $simulation
     * @return MailBoxModel
     */
    public static function sendMs32($simulation)
    {
        $senderId = Characters::model()->findByAttributes(['code' => Characters::HERO_ID])->primaryKey;
        $receiverId = Characters::model()->findByAttributes(['code' => '12'])->primaryKey;

        $msgParams = [
            'simId' => $simulation->id,
            'subject_id' => CommunicationTheme::model()->findByAttributes([
                'code'=>55,
                'character_id' => $receiverId,
                'mail_prefix'=>'rere'
            ])->primaryKey,
            'message_id' => 0,
            'receivers' => $receiverId,
            'group' => MailBoxModel::OUTBOX_FOLDER_ID,
            'sender' => $senderId,
            'time' => '11:00',
            'letterType' => null
        ];

        return MailBoxService::sendMessage($msgParams);
    }

    /**
     * @param Simulation $simulation
     * @return MailBoxModel
     */
    public static function sendMs40($simulation)
    {
        $mailService = new MailBoxService();
        $character = Characters::model()->findByAttributes(['code' => 9]);

        return $mailService->sendMessage([
            'subject_id' => CommunicationTheme::model()->findByAttributes([
                'code' => 5,
                'character_id' => $character->primaryKey,
                'mail_prefix' => 're'
            ])->primaryKey,
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
    }

    /**
     * @param Simulation $simulation
     * @return MailBoxModel
     */
    public static function sendMs55($simulation)
    {
        $mail = new MailBoxService();

        return $mail->sendMessage([
            'subject_id' => CommunicationTheme::model()->findByAttributes(['code' => 71])->primaryKey,
            'message_id' => MailTemplateModel::model()->findByAttributes(['code' => 'MS55']),
            'receivers' => Characters::model()->findByAttributes(['code' => 39])->primaryKey,
            'sender' => Characters::model()->findByAttributes(['code' => 1])->primaryKey,
            'time' => '11:00:00',
            'group' => 3,
            'letterType' => 'new',
            'simId' => $simulation->primaryKey
        ]);
    }
}