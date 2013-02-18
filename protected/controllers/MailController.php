<?php

/**
 * Контроллер почтовика
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailController extends AjaxController
{

    /**
     * @deprecated: use getMessages instead of this.
     * Отдачи состава папок
     */
    /*public function actionGetFolders()
    {
        $simulation = $this->getSimulationEntity();

        list($folders, $messages) = MailBoxService::getFolders($simulation);

        return $this->sendJSON(array(
            'result'   => 1,
            'folders'  => $folders,
            'messages' => $messages,
        ));
    }*/

    /**
     * Возвращает колличество непрочитанных писем во входящих
     */
    public function actionGetInboxUnreadCount()
    {
        $simulation = $this->getSimulationEntity();
        
        $result = array('result' => 0);
        
        $unreadInfo = MailBoxService::getFoldersUnreadCount($simulation->id);

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

        $order = Yii::app()->request->getParam('order', false);
        $orderType = Yii::app()->request->getParam('order_type', 'ASC');
        $messages = MailBoxService::getMessages(array(
            'folderId'   => $folderId,
            'receiverId' => SessionHelper::getUidBySid(),
            'order'      => Yii::app()->request->getParam('order', false),
            'orderType'  => Yii::app()->request->getParam('order_type', 'ASC'),
            'uid'        => SessionHelper::getUidBySid(),
            'simId'      => $simulation->id
        ));
        $this->sendJSON(array(
            'result'   => 1,
            'messages' => $messages,
            'type'     => MailBoxModel::$folderIdToAlias[$folderId]
        ));
    }

    /**
     * 
     */
    public function actionGetMessage()
    {
        $this->sendJSON(array(
            'result' => 1,
            'data'   => MailBoxService::getMessage((int)Yii::app()->request->getParam('emailId', 0)
        )));
    }

    /**
     * Получение списка получателей.
     * @return type 
     */
    public function actionGetReceivers()
    {
        $this->sendJSON(array(
            'result' => 1,
            'data'   => MailBoxService::getCharacters()
        ));
    }

    /**
     * 
     * @return type
     */
    public function actionGetPhrases()
    {
        $characterThemeId = (int) Yii::app()->request->getParam('id', 0);
        $forwardLetterCharacterThemesId = (int) Yii::app()->request->getParam('forwardLetterCharacterThemesId', 0);
         
        return $this->sendJSON(array_merge(
            array('result' => 1), 
            MailBoxService::getPhrases($characterThemeId, $forwardLetterCharacterThemesId)
        ));
    }

    /**
     * 
     */
    public function actionSendMessage()
    {
        $simulation = $this->getSimulationEntity();
        
        // sendMessagePro has a lot of option, and we need to validate it
        // so I agregate them to supply-object SendMailOptions
        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray(Yii::app()->request->getParam('receivers', ''));
            
        if (false === $sendMailOptions->isValidRecipientsArray()) {
            $this->returnErrorMessage(null, "Не указан хотя бы один получатель!");
        }

        // more sendMessagePro options
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = Yii::app()->request->getParam('messageId', 0);
        $sendMailOptions->time = Yii::app()->request->getParam('time', NULL);
        $sendMailOptions->copies     = Yii::app()->request->getParam('copies', array());
        $sendMailOptions->phrases    = Yii::app()->request->getParam('phrases', array());
        $sendMailOptions->fileId     = (int)Yii::app()->request->getParam('fileId', 0);
        $sendMailOptions->subject_id    = Yii::app()->request->getParam('subject', NULL);
        
        $sendMailOptions->setLetterType(Yii::app()->request->getParam('letterType', NULL));
        
        // key Action
        $message = MailBoxService::sendMessagePro($sendMailOptions);
        
        if (NULL === $message){
            $this->sendJSON(array('result' => 0));
        } else {
            $this->sendJSON(array(
                'result'    => 1,
                'messageId' => $message->id
            ));
        }
    }

    /**
     * 
     */
    public function actionSaveDraft()
    {
        $simulation = $this->getSimulationEntity();
        
        $sendMailOptions = new SendMailOptions();
        $sendMailOptions->setRecipientsArray(Yii::app()->request->getParam('receivers', ''));
        $sendMailOptions->simulation = $simulation;
        $sendMailOptions->messageId  = Yii::app()->request->getParam('messageId', 0);
        $sendMailOptions->time = Yii::app()->request->getParam('time', NULL);
        $sendMailOptions->copies     = Yii::app()->request->getParam('copies', array());
        $sendMailOptions->phrases    = Yii::app()->request->getParam('phrases', array());
        $sendMailOptions->fileId     = (int)Yii::app()->request->getParam('fileId', 0);
        $sendMailOptions->subject_id    = Yii::app()->request->getParam('subject', NULL);
        $sendMailOptions->setLetterType(Yii::app()->request->getParam('letterType', NULL));


        $email = MailBoxService::saveDraft($sendMailOptions);

        $this->sendJSON(array(
            'result' => (NULL === $email) ? 0 : 1,
            'messageId' => $email->id
        ));
    }

    /**
     * Возвращает настройки почты
     * @return type 
     */
    public function actionGetSettings()
    {
        $simulation = $this->getSimulationEntity();
        
        $mailClientSettingsEntity = MailSettingsModel::model()
            ->bySimulation($simulation->id)
            ->find();
        
        if (NULL === $mailClientSettingsEntity){
            $this->sendJSON(array('result' => 0));
        } else {
            $this->sendJSON(array(
                'result'  => 1,
                'data'    =>  $mailClientSettingsEntity->getSettingsArray()
            ));
        }
    }

    /**
     * Сохранение настроек почты
     * @return type 
     */
    public function actionSaveSettings()
    {
        $simulation = $this->getSimulationEntity();
        
        $this->sendJSON(array(
            'result' => (int)MailSettingsModel::updateSimulationSettings(
                $simulation, 
                Yii::app()->request->getParam('messageArriveSound', 0)
             )
        ));
    }

    /**
     * Получение тем
     */
    public function actionGetThemes()
    {
        $this->sendJSON(array(
            'result'           => 1,
            'data'             => MailBoxService::getThemes(
                Yii::app()->request->getParam('receivers', ''),
                Yii::app()->request->getParam('parentSubjectId', null)
            ),
            'characterThemeId' => CommunicationTheme::getCharacterThemeId(
                Yii::app()->request->getParam('receivers', ''), 
                Yii::app()->request->getParam('parentSubjectId', 0)
            ),
        ));
    }

    /**
     * 
     * @return type
     */
    public function actionDelete()
    {
        $simulation = $this->getSimulationEntity();

        return $this->sendJSON(array(
            'result'  => (int)MailBoxService::delete((int)Yii::app()->request->getParam('id', 0)),
            'folders' => MailBoxService::getFolders($simulation)
        ));
    }

    /**
     * @return type
     */
    public function actionMarkRead()
    {
        $simulation = $this->getSimulationEntity();
        
        return $this->sendJSON(array(
            'result'  => (int)MailBoxService::markReaded((int)Yii::app()->request->getParam('id', 0)),
            'folders' => MailBoxService::getFolders($simulation),
        ));
    }

    /**
     * @return type
     */
    public function actionMarkPlanned()
    {
        $simulation = $this->getSimulationEntity();
        
        return $this->sendJSON(array(
            'result'  => (int)MailBoxService::markPlanned((int)Yii::app()->request->getParam('emailId', 0)),
        ));
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
        try {
            return $this->sendJSON(array(
                'result'  => MailBoxService::moveToFolder(
                    MailBoxModel::model()->findByPk((int)Yii::app()->request->getParam('messageId', 0)), 
                    Yii::app()->request->getParam('folderId', NULL)
                ),
                'folders' => MailBoxService::getFolders($simulation),
            ));
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }

    /**
     * @return array
     */
    public function actionReply()
    {

        $messageToReply = MailBoxModel::model()
            ->findByPk(Yii::app()->request->getParam('id', 0));

        $characters = MailBoxService::getCharacters();
        $subjectEntity = MailBoxService::getSubjectForRepryEmail($messageToReply);

        $this->sendJSON(array(
            'result'      => 1,
            'subjectId'   => (null === $subjectEntity) ? null : $subjectEntity->id,
            'subject'     => (null === $subjectEntity) ? null : $subjectEntity->getFormattedTheme(),
            'receiver'    => $characters[$messageToReply->sender_id],
            'receiver_id' => $messageToReply->sender_id,
            'phrases'     => MailBoxService::getPhrasesDataForReply( $messageToReply, $subjectEntity )
        ));
    }

    public function actionReplyAll()
    {
        $simulation = $this->getSimulationEntity();
        
        $messageToReply = MailBoxModel::model()
            ->findByPk(Yii::app()->request->getParam('id', 0));
         
        $characters = MailBoxService::getCharacters();
        $subjectEntity = MailBoxService::getSubjectForRepryEmail($messageToReply);
        list($copiesIds, $copies) = MailBoxService::getCopiesArrayForReplyAll($messageToReply);

        return $this->sendJSON(array(
            'result'      => 1,
            'subjectId'   => (null === $subjectEntity) ? null : $subjectEntity->id,
            'subject'     => (null === $subjectEntity) ? null : $subjectEntity->getFormattedTheme(),
            'receiver'    => $characters[$messageToReply->sender_id],
            'receiver_id' => $messageToReply->sender_id,
            'copiesIds'   => $copiesIds,
            'copies'      => $copies,
            'phrases'     => MailBoxService::getPhrasesDataForReply( $messageToReply, $subjectEntity)
        ));
    }

    /**
     * Получение списка потенциальных задач для добавления в план
     */
    public function actionToPlan()
    {
        $simulation = $this->getSimulationEntity();
        
        $email = MailBoxModel::model()
            ->findByPk(Yii::app()->request->getParam('id', 0));
        
        $this->sendJSON(array(
            'result' => 1,
            'data'   => MailBoxService::getListTasksAvailableToPlanning($email),
        ));        
    }

    /**
     * Добавление задачи в план
     */
    public function actionAddToPlan()
    {
        $simulation = $this->getSimulationEntity();
        
        $email = MailBoxModel::model()
            ->findByPk(Yii::app()->request->getParam('messageId', 0));

        assert($email);
        
        $emailTask = MailTasksModel::model()->findByPk(Yii::app()->request->getParam('id', 0));
        assert($emailTask);
        
        $plannerTask = MailBoxService::addMailTaskToPlanner($simulation, $email, $emailTask);
        
        $this->sendJSON(array(
            'result' => (NULL === $plannerTask) ? 0 : 1,
            'taskId' => (NULL === $plannerTask) ? NULL : $plannerTask->id,
        ));
    }
    
    /**
     * @deprecated is this method in use?
     * Добавление задачи в план
     */
    /*public function actionGetTaskInfo()
    {
        $simulation = $this->getSimulationEntity();
        
        $email = MailBoxModel::model()
            ->findByPk(Yii::app()->request->getParam('messageId', 0));
        
        $emailTask = MailTasksModel::model()->findByPk(Yii::app()->request->getParam('id', 0));
        
        $this->sendJSON(array(
            'result' => (NULL === $emailTask ) ? 0 : 1,
            'task' => [
                'id'       => (NULL === $emailTask ) ? NULL : $emailTask->id,
                'duration' => (NULL === $emailTask ) ? NULL : $emailTask->duration,
                'day'      => (NULL === $emailTask ) ? NULL : $emailTask->day,
                'text'     => (NULL === $emailTask ) ? NULL : $emailTask->name,
                'date'     => (NULL === $emailTask ) ? NULL : $emailTask->date,
            ]
        ));
    }*/

    /**
     *  Отправить из черновиков
     */
    public function actionSendDraft()
    {
        $simulation = $this->getSimulationEntity();
        $email = MailBoxModel::model()->findByPk((int)Yii::app()->request->getParam('id', 0));
        
        $this->sendJSON(array(
            'result' => (int)MailBoxService::sendDraft($simulation, $email),
        ));
    }

    /**
     * Переслать письмо
     * @return type 
     */
    public function actionForward()
    {
        $simulation = $this->getSimulationEntity();

        $messageToForward = MailBoxModel::model()->findByPk((int)Yii::app()->request->getParam('id', 0));
        
        $this->sendJSON(
            MailBoxService::getForwardMessageData($simulation, $messageToForward)
        );
    }
}
