<?php

class MailController extends SimulationBaseController
{
    /**
     * Возвращает колличество непрочитанных писем во входящих
     */
    public function actionGetInboxUnreadCount()
    {
        $simulation = $this->getSimulationEntity();
        
        $result = array('result' => 0);
        
        $unreadInfo = MailBoxService::getFoldersUnreadCount($simulation);

        if (isset($unreadInfo[1])) {
            $result = array(
                'result' => 1,
                'unreaded' => $unreadInfo[1]
            );
        }
        
        $this->sendJSON($result);
    }

    /**
     * Получение списка сообщений
     */
    public function actionGetMessages()
    {
        $simulation = $this->getSimulationEntity(); 
        $folderId = (int) Yii::app()->request->getParam('folderId');

        if (0 == $folderId) {
            $folderId = 1;
        }

        $order = Yii::app()->request->getParam('order', false);
        $orderType = Yii::app()->request->getParam('order_type', 'ASC');
        $messages = MailBoxService::getMessages(array(
            'folderId'   => $folderId,
            'order'      => $order,
            'orderType'  => $orderType,
            'uid'        => Yii::app()->user->id,
            'simId'      => $simulation->id
        ));
        $result = [
            'result'   => 1,
            'messages' => $messages,
            'count'    => count($messages),
            'type'     => MailBox::$folderIdToAlias[$folderId]
        ];
        $this->sendJSON($result);
    }

    /**
     * 
     * @return type
     */
    public function actionGetPhrases()
    {
        $simulation = $this->getSimulationEntity();
        $characterThemeId = (int) Yii::app()->request->getParam('id', 0);
        $forwardLetterCharacterThemesId = (int) Yii::app()->request->getParam('forwardLetterCharacterThemesId', 0);

        $result = array_merge(
            ['result' => self::STATUS_SUCCESS],
            MailBoxService::getPhrases($characterThemeId, $forwardLetterCharacterThemesId, $simulation)
        );
        $this->sendJSON($result);
    }

    /**
     * 
     */
    public function actionSendMessage()
    {
        $simulation = $this->getSimulationEntity();
        
        // sendMessagePro has a lot of option, and we need to validate it
        // so I agregate them to supply-object SendMailOptions
        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray(Yii::app()->request->getParam('receivers', ''));
            
        if (false === $sendMailOptions->isValidRecipientsArray()) {
            $this->returnErrorMessage(null, "Не указан хотя бы один получатель!");
        }

        // more sendMessagePro options
        $sendMailOptions->simulation   = $simulation;
        $sendMailOptions->messageId    = Yii::app()->request->getParam('messageId', 0);
        $sendMailOptions->time         = Yii::app()->request->getParam('time', NULL);
        $sendMailOptions->copies       = Yii::app()->request->getParam('copies', array());
        $sendMailOptions->phrases      = Yii::app()->request->getParam('phrases', array());
        $sendMailOptions->fileId       = (int)Yii::app()->request->getParam('fileId', 0);
        $sendMailOptions->subject_id   = Yii::app()->request->getParam('subject', NULL);

        $sendMailOptions->setLetterType(Yii::app()->request->getParam('letterType', NULL));
        
        // key Action
        $message = MailBoxService::sendMessagePro($sendMailOptions);
        
        if (NULL === $message) {
            $result = ['result' => self::STATUS_ERROR];
            $this->sendJSON($result);
        } else {
            $result = ['result' => self::STATUS_SUCCESS ,'messageId' => $message->id];
            $this->sendJSON($result);
        }
    }

    /**
     * 
     */
    public function actionSaveDraft()
    {
        $simulation = $this->getSimulationEntity();
        
        $sendMailOptions = new SendMailOptions($simulation);
        $sendMailOptions->setRecipientsArray(Yii::app()->request->getParam('receivers', ''));
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = Yii::app()->request->getParam('messageId', 0);
        $sendMailOptions->time = Yii::app()->request->getParam('time', NULL);
        $sendMailOptions->copies     = Yii::app()->request->getParam('copies', array());
        $sendMailOptions->phrases    = Yii::app()->request->getParam('phrases', array());
        $sendMailOptions->fileId     = (int)Yii::app()->request->getParam('fileId', 0);
        $sendMailOptions->subject_id = Yii::app()->request->getParam('subject', NULL);
        $sendMailOptions->id         = Yii::app()->request->getParam('id', NULL);
        $sendMailOptions->setLetterType(Yii::app()->request->getParam('letterType', NULL));

        $email = MailBoxService::saveDraft($sendMailOptions);

        $result = ['result' => (NULL === $email) ? self::STATUS_ERROR : self::STATUS_SUCCESS,'messageId' => $email->id];
        $this->sendJSON($result);
    }

    /**
     * Получение тем
     */
    public function actionGetThemes()
    {
        $result = [
            'result'           => self::STATUS_SUCCESS,
            'data'             => MailBoxService::getThemes(
                $this->getSimulationEntity(),
                Yii::app()->request->getParam('receivers', ''),
                Yii::app()->request->getParam('parentSubjectId', null)
            )
        ];
        $this->sendJSON($result);
    }

    /**
     * @return type
     */
    public function actionMarkRead()
    {
        $result = array(
            'result'  => (int)MailBoxService::markReaded((int)Yii::app()->request->getParam('id', 0))
        );
        $this->sendJSON($result);
    }

    /**
     * Перенести письмо в другую папку
     * @todo: check is it in use?
     * How can I move letter. Just Delete(move to trash), Save or Send. 
     * But we have conthollers for each action (delete, save, send).
     */
    public function actionMove()
    {
        $simulation = $this->getSimulationEntity();
        $result = [
            'result'  => MailBoxService::moveToFolder(
                MailBox::model()->findByPk((int)Yii::app()->request->getParam('messageId', 0)),
                Yii::app()->request->getParam('folderId', NULL)
            ),
            'folders' => [
                'inbox' => MailBoxService::getMessages([
                    'folderId'   => MailBox::FOLDER_INBOX_ID,
                    'simId'     => $simulation->id
                ]),
                'sended' => MailBoxService::getMessages([
                    'folderId'   => MailBox::FOLDER_OUTBOX_ID,
                    'simId'     => $simulation->id
                ]),
            ]
        ];
        $this->sendJSON($result);
    }

    /**
     * @return array
     */
    public function actionReply()
    {
        $messageToReply = MailBox::model()->findByPk(Yii::app()->request->getParam('id', 0));

        $result = MailBoxService::getMessageData($messageToReply, MailBoxService::ACTION_REPLY);
        $this->sendJSON($result);
    }

    public function actionReplyAll()
    {
        $messageToReply = MailBox::model()->findByPk(Yii::app()->request->getParam('id', 0));
        $result = MailBoxService::getMessageData($messageToReply, MailBoxService::ACTION_REPLY_ALL);

        $this->sendJSON($result);
    }

    public function actionEdit()
    {
        $message = MailBox::model()->findByPk(Yii::app()->request->getParam('id', 0));

        $result = MailBoxService::getMessageData($message, MailBoxService::ACTION_EDIT);
        $this->sendJSON($result);
    }

    /**
     * Получение списка потенциальных задач для добавления в план
     */
    public function actionToPlan()
    {
        $simulation = $this->getSimulationEntity();
        
        $email = MailBox::model()
            ->findByPk(Yii::app()->request->getParam('id', 0));

        $result = [
            'result' => self::STATUS_SUCCESS,
            'data'   => MailBoxService::getListTasksAvailableToPlanning($email),
        ];
        $this->sendJSON($result);
    }

    /**
     * Добавление задачи в план
     */
    public function actionAddToPlan()
    {
        $simulation = $this->getSimulationEntity();
        
        $email = MailBox::model()
            ->findByPk(Yii::app()->request->getParam('messageId', 0));

        assert($email);
        
        $emailTask = MailTask::model()->findByPk(Yii::app()->request->getParam('id', 0));
        assert($emailTask);
        
        $plannerTask = MailBoxService::addMailTaskToPlanner($simulation, $email, $emailTask);

        $json = [
            'result' => (NULL === $plannerTask) ? self::STATUS_ERROR : self::STATUS_SUCCESS,
            'taskId' => (NULL === $plannerTask) ? NULL : $plannerTask->id,
        ];
        $this->sendJSON($json);
    }

    /**
     *  Отправить из черновиков
     */
    public function actionSendDraft()
    {
        $simulation = $this->getSimulationEntity();
        $email = MailBox::model()->findByPk((int)Yii::app()->request->getParam('id', 0));

        $json = [
            'result' => MailBoxService::sendDraft(
                $simulation,
                $email,
                Yii::app()->request->getParam('screen', null)
            ),
        ];
        $this->sendJSON($json);
    }

    /**
     * Переслать письмо
     * @return type 
     */
    public function actionForward()
    {
        $messageToForward = MailBox::model()->findByPk(Yii::app()->request->getParam('id', 0));

        $json = MailBoxService::getMessageData($messageToForward, MailBoxService::ACTION_FORWARD);

        $this->sendJSON($json);
    }

    /**
     * 
     */
    public function actionSendMsInDevMode()
    {
        $msCode = Yii::app()->request->getParam('msCode', NULL);
        $time = (int)Yii::app()->request->getParam('time', NULL);
        $windowId = (int)Yii::app()->request->getParam('windowId', NULL);
        $subWindowId = (int)Yii::app()->request->getParam('subWindowId', NULL);
        $windowUid = (int)Yii::app()->request->getParam('windowUid', NULL);

        $simulation = $this->getSimulationEntity();
        $message = LibSendMs::sendMsByCode($simulation, $msCode, $time, $windowId, $subWindowId, $windowUid);

        if (NULL !== $message) {
            $json = ['result' => self::STATUS_SUCCESS];
        } else {
            $json = ['result' => self::STATUS_ERROR];
        }
        $this->sendJSON($json);
    }
}
