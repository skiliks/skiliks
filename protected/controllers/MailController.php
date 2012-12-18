<?php

/**
 * Контроллер почтовика
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailController extends AjaxController{
    
    /**
     * Отдачи состава папок
     */
    public function actionGetFolders() {
        
        $sid = Yii::app()->request->getParam('sid', false);  
        $receiverId = 1; // герой SessionHelper::getUidBySid($sid);
        try {
            $simId = SessionHelper::getSimIdBySid($sid);
        } catch (CException $e) {
            return $this->sendJSON(
                array(
                    'result' => 0,
                    'e'      => $e->getMessage()
                )
            );
        }
            
        $service = new MailBoxService();
        $folders = $service->getFolders();
        
        
        // добавляем информацию о колличестве непрочитанных сообщений в подпапках
        $unreadInfo = MailBoxService::getFoldersUnreadCount($simId);
        foreach($unreadInfo as $folderId => $count) {
            if ($folderId > 0)
                $folders[$folderId]['unreaded'] = $count;
        }
        
        
        $result = array();
        $result['result'] = 1;
        $result['folders'] = $folders;
        $result['messages'] = $service->getMessages(array(
            'folderId' => $folders[1]['id'],
            'receiverId' => $receiverId,
            'simId' => $simId
        ));
        
        return $this->sendJSON($result);
    }
    
    /**
     * Возвращает колличество непрочитанных писем во входящих
     */
    public function actionGetInboxUnreadedCount() {
        
        $sid = Yii::app()->request->getParam('sid', false);  
        try {
            $simId = SessionHelper::getSimIdBySid($sid);
        } catch (CException $e) {
            return $this->sendJSON(
                array(
                    'result' => 0,
                    'e'      => $e->getMessage()
                )
            );
        }
        $unreadInfo = MailBoxService::getFoldersUnreadCount($simId);
        
        $result = array();
        $result['result'] = 0;
        if (isset($unreadInfo[1])) {
            $result['result'] = 1;
            $result['unreaded'] = $unreadInfo[1];
            return $this->sendJSON($result);
        }
        return $this->sendJSON($result);
    }
    
    /**
     * Получение списка сообщений
     */
    public function actionGetMessages() {
        
        $sid = Yii::app()->request->getParam('sid', false);  
        $folderId = (int)Yii::app()->request->getParam('folderId', false);  
        $order = Yii::app()->request->getParam('order', false);  
        $orderType = (int)Yii::app()->request->getParam('order_type', false);  
        
        $receiverId = SessionHelper::getUidBySid();
        try {
            $simId = SessionHelper::getSimIdBySid($sid);
        } catch (CException $e) {
            return $this->sendJSON(
                array(
                    'result' => 0,
                    'e'      => $e->getMessage()
                )
            );
        }
        
        $service = new MailBoxService();
        
        $result = array();
        $result['result'] = 1;
        $result['messages'] = $service->getMessages(array(
            'folderId' => $folderId,
            'receiverId' => $receiverId,
            'order' => $order,
            'orderType' => $orderType,
            'uid' => $receiverId,
            'simId' => $simId
        ));
        
        if ($folderId == 1) {
            $result['type'] = 'inbox';
        }
        elseif ($folderId == 2) {
            $result['type'] = 'drafts';
        }
        elseif ($folderId == 3) {
            $result['type'] = 'outbox';
        }
        elseif ($folderId == 4) {
            $result['type'] = 'inbox';
        }
        
        return $this->sendJSON($result);
    }
    
    public function actionGetMessage() {
        
        $id = (int)Yii::app()->request->getParam('id', false);  
        
        $service = new MailBoxService();
        $message = $service->getMessage($id);

        $result = array();
        $result['result'] = 1;
        $result['data'] = $message;
        return $this->sendJSON($result);
    }
    
    /**
     * Получение списка получателей.
     * @return type 
     */
    public function actionGetReceivers() {
        
        $service = new MailBoxService();
        
        $result = array();
        $result['result'] = 1;
        $result['data'] = $service->getCharacters();
        return $this->sendJSON($result);
    }
    
    /**
     * 
     * @return type
     */
    public function actionGetPhrases() 
    {
        $character_theme_id = (int)Yii::app()->request->getParam('id', false);
        $forwardLetterCharacterThemesId = (int)Yii::app()->request->getParam('forwardLetterCharacterThemesId', false);
        $service = new MailBoxService();
        
        // for forwarded letters        
        if ((int)$character_theme_id === 0 && (int)$forwardLetterCharacterThemesId !== 0) {
            $character_theme_id = $forwardLetterCharacterThemesId;
        }
        
        if ((int)$character_theme_id === 0) {
            $this->sendJSON(array(
                'result' => 1,
                'data'    => $service->getMailPhrases(),
                'addData' => $service->getSigns()
            ));
        }
        
        $character_theme = MailCharacterThemesModel::model()->findByPk($character_theme_id); 

        $result = array();
        $result['result'] = 1;
        if ('TXT' === $character_theme->constructor_number) {
            $result['message'] = $character_theme->letter->message;
        } else {
            $result['data'] = $service->getMailPhrases($character_theme_id);
            $result['addData'] = $service->getSigns();
        }
        return $this->sendJSON($result);
    }
    
    public function actionSendMessage() 
    {
        $result['result'] = 1;
        try {
            $sid = Yii::app()->request->getParam('sid', false);

            $senderId = SessionHelper::getUidBySid($sid);
            $simId = SessionHelper::getSimIdBySid($sid);

            $messageId = Yii::app()->request->getParam('messageId', false);
            $timeString = Yii::app()->request->getParam('timeString', false); 
            $receivers = Yii::app()->request->getParam('receivers', false);

            if(empty($receivers)) {
                throw new Exception("Не указан хотя бы один получатель!");
            }
            $copies = Yii::app()->request->getParam('copies', false);           
            $phrases = Yii::app()->request->getParam('phrases', false);          
            $letterType = Yii::app()->request->getParam('letterType', false);  
            $fileId = (int)Yii::app()->request->getParam('fileId', false);

            if($letterType == 'reply' OR $letterType == 'replyAll'){
                if(!empty($messageId)){
                    //Изменяем запись в бд: SK - 708
                    $message = MailBoxModel::model()->byId($messageId)->find();
                    $message->reply = 1;//1 - значит что на сообщение отправлен ответ
                    $message->update();
                }else{
                    throw new Exception("Ошибка, не указан messageId для ответить или ответить всем");
                }
            }

            list($subject_id, $subject) = $this->checkSubject($letterType, Yii::app()->request->getParam('subject', null));
            
            $service = new MailBoxService();
            $message = $service->sendMessage(array(
                'message_id' => $messageId,
                'group'      => 3, // outbox
                'sender'     => 1, //$senderId, //- отправитель теперь всегда герой
                'receivers'  => $receivers,
                'copies'     => $copies,
                'subject'    => $subject,
                'subject_id' => $subject_id,
                'phrases'    => $phrases,
                'simId'      => $simId,
                'letterType' => $letterType,
                'fileId'     => $fileId,
                'timeString' => $timeString
            ));
            $result['messageId'] = $message->primaryKey;
        } catch (Exception $e) {
            $result['result'] = 0;
            $result['messsage'] = $e->getMessage();
        }
        
        $this->sendJSON($result);
    }
    
    public function actionSaveDraft() 
    {
        $sid = Yii::app()->request->getParam('sid', false);  
        
        try {
            $simId = SessionHelper::getSimIdBySid($sid);
        } catch (CException $e) {
            return $this->sendJSON(
                array(
                    'result' => 0,
                    'e'      => $e->getMessage()
                )
            );
        }        
        
        $messageId = Yii::app()->request->getParam('messageId', false);
        $timeString = Yii::app()->request->getParam('timeString', false); 
        $receivers = Yii::app()->request->getParam('receivers', false);  
        $copies = Yii::app()->request->getParam('copies', false);  
        $phrases = Yii::app()->request->getParam('phrases', false);  
        $letterType = Yii::app()->request->getParam('letterType', false);
        $fileId = (int)Yii::app()->request->getParam('fileId', false);

        list($subject_id, $subject) = $this->checkSubject($letterType, Yii::app()->request->getParam('subject', null));
       
        $service = new MailBoxService();
        $service->sendMessage(array(
            'message_id' => $messageId,
            'group' => 2,
            'sender' => 1, // черновики писать может только главгый герой
            'receivers' => $receivers,
            'copies' => $copies,
            'subject' => $subject,
            'subject_id' => $subject_id,
            'phrases' => $phrases,
            'simId' => $simId,
            'timeString'=>$timeString,
            'fileId' => $fileId,
            'letterType'=>$letterType
        ));
        
        // @todo: what is in error case?
        $this->sendJSON(array('result' => 1));
    }

    /**
     *
     * @param string $emailType, 'new','forward','reply','replyAll', etc.
     * @param $subjectFromRequest
     * @return array
     */
    private function checkSubject($emailType, $subjectFromRequest) 
    {
        if ('new' === $emailType) {  
            // check is this id of predefined subjects (table 'mail_character_themes')
            $emailToCharacterSubject = MailCharacterThemesModel::model()->findByPk((int)$subjectFromRequest);
            if (null !== $emailToCharacterSubject) {
                // get real subject id (id for nable 'mail_themes')              
                $subject_id = $emailToCharacterSubject->theme_id;
                $subject = null;
            } else {
                // this is TEXT subject
                $subject_id = null;
                $subject = $subjectFromRequest;
            }
        } else {
            $subject_id = (int)$subjectFromRequest;
            $subject = null;
        }
        
        if (0 === (int)$subject_id && null === $subject) {
            
            $subjectObject = MailThemesModel::model()
                ->byName($subjectFromRequest)
                ->find();
            if (null !== $subjectObject) {
                $subject_id = $subjectObject->id;
                $subject    = $subjectObject->name;
            }
        }
        
        //var_dump($subject_id, $subject); die;
        
        return array($subject_id, $subject);
    }

    /**
     * Возвращает настройки почты
     * @return type 
     */
    public function actionGetSettings() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);  
            $simId = SessionHelper::getSimIdBySid($sid);

            $model = MailSettingsModel::model()->bySimulation($simId)->find();
            //var_dump($model);die();
            $result = array();
            $result['result'] = 1;
            $result['data'] = array(
                'messageArriveSound' => $model->messageArriveSound
            );
            $this->sendJSON($result);
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            $this->sendJSON($result);
        }    
    }
    
    /**
     * Сохранение настроек почты
     * @return type 
     */
    public function actionSaveSettings() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);  
            $simId = SessionHelper::getSimIdBySid($sid);
            
            $messageArriveSound = (int)Yii::app()->request->getParam('messageArriveSound', false);  

            $model = MailSettingsModel::model()->bySimulation($simId)->find();
            $model->messageArriveSound = $messageArriveSound;
            $model->update();
            
            //var_dump($model);die();
            $result = array();
            $result['result'] = 1;
            return $this->sendJSON($result);
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->sendJSON($result);
        }    
    }
    
    /**
     * Получение тем
     */
    public function actionGetThemes() 
    {
        $receivers   = Yii::app()->request->getParam('receivers', false);  
        $mailThemeId = Yii::app()->request->getParam('forwardEmailId', false); 
        
        $characterThemeId = null;
        $receiversArr = explode(',', $receivers);
        
        if (0 < count($receiversArr) && null != $mailThemeId) {
            $characterTheme = MailCharacterThemesModel::model()
                ->byCharacter(reset($receiversArr))
                ->byTheme($mailThemeId)
                ->find();
            
            if (null !== $characterTheme) {
                $characterThemeId = $characterTheme->id;
            }
        }        
        
        $service = new MailBoxService();
        
        $result = array(
            'result'           => 1,
            'data'             => $service->getThemes($receivers),
            'characterThemeId' => $characterThemeId,
        );
        return $this->sendJSON($result);
    }
    
    public function actionDelete() {
        $id = (int)Yii::app()->request->getParam('id', false);  
        $sid = Yii::app()->request->getParam('sid', false); 
        try {
            $simId = SessionHelper::getSimIdBySid($sid);
        } catch (CException $e) {
            return $this->sendJSON(
                array(
                    'result' => 0,
                    'e'      => $e->getMessage()
                )
            );
        }
        
        $service = new MailBoxService();
        $service->delete($id);
        
        $service = new MailBoxService();
        $foldersInfo = $service->getFolders();
        $folders = array();
        foreach($foldersInfo as $folderId=>$item) {
            $folders[$folderId] = array(
                'folderId' => $folderId,
                'unreaded' => 0
            );
        }
        
        $result = array();
        $result['result'] = 1;        
        $unreadInfo = MailBoxService::getFoldersUnreadCount($simId);
        foreach($unreadInfo as $folderId => $count) {
            $folders[$folderId]['unreaded'] = $count;
        }
        $result['folders'] = $folders;        
        
        return $this->sendJSON($result);
    }
    
    public function actionMarkRead() {
        $id = (int)Yii::app()->request->getParam('id', false);  
        $sid = Yii::app()->request->getParam('sid', false);  
        try {
            $simId = SessionHelper::getSimIdBySid($sid);
        } catch (CException $e) {
            return $this->sendJSON(
                array(
                    'result' => 0,
                    'e'      => $e->getMessage()
                )
            );
        }
        
        $service = new MailBoxService();
        $service->setAsReaded($id);
        
        $service = new MailBoxService();
        $foldersInfo = $service->getFolders();
        $folders = array();
        foreach($foldersInfo as $folderId=>$item) {
            $folders[$folderId] = array(
                'folderId' => $folderId,
                'unreaded' => 0
            );
        }
        
        $result = array();
        $result['result'] = 1;        
        $unreadInfo = MailBoxService::getFoldersUnreadCount($simId);
        foreach($unreadInfo as $folderId => $count) {
            $folders[$folderId]['unreaded'] = $count;
        }
        $result['folders'] = $folders;        
        
        return $this->sendJSON($result);
    }
    
    /**
     * Перенести письмо в другую папку
     */
    public function actionMove() {
        try {
            $messageId = (int)Yii::app()->request->getParam('messageId', false);  
            $folderId = (int)Yii::app()->request->getParam('folderId', false);  
            $sid = Yii::app()->request->getParam('sid', false); 
            
            try {
               $simId = SessionHelper::getSimIdBySid($sid);
            } catch (CException $e) {
                return $this->sendJSON(
                    array(
                        'result' => 0,
                        'e'      => $e->getMessage()
                    )
                );
            }       
            
            $model = MailBoxModel::model()->byId($messageId)->find();
            if (!$model) {
                throw new Exception("cant find model by id : {$messageId}");
            }
            
            
            
            if ($model->group_id > 1 && $model->group_id < 4) {
                return $this->sendJSON(array('result'=>0));
            }
            
            if ($folderId > 1 && $folderId < 4) {
                return $this->sendJSON(array('result'=>0));
            }
            
            /*
            // проверка, а можем ли мы это письмо перемещать
            if ($model->group_id == 1) {
                // из входящих можно перемещать только в корзину
                if ($folderId != 4) {
                    return $this->sendJSON(array('result'=>0));
                }
            }
            if ($model->group_id == 4) {
                // из корзины можно перемещать только во входящие
                if ($folderId != 1) {
                    return $this->sendJSON(array('result'=>0));
                }
            }*/
            
            $model->group_id = $folderId;
            $model->save();
            
            $service = new MailBoxService();
            $foldersInfo = $service->getFolders();
            $folders = array();
            foreach($foldersInfo as $folderId=>$item) {
                $folders[$folderId] = array(
                    'folderId' => $folderId,
                    'unreaded' => 0
                );
            }

            $result = array();
            $result['result'] = 1;        
            $unreadInfo = MailBoxService::getFoldersUnreadCount($simId);
            foreach($unreadInfo as $folderId => $count) {
                $folders[$folderId]['unreaded'] = $count;
            }
            $result['folders'] = $folders;        
        
            return $this->sendJSON($result);
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->sendJSON($result);
        }
    }
    
    public function actionReply() {
        $messageId = (int)Yii::app()->request->getParam('id', false);
        $sid = Yii::app()->request->getParam('sid', false);
        try {
           $simId = SessionHelper::getSimIdBySid($sid);
        } catch (CException $e) {
            return $this->sendJSON(
                array(
                    'result' => 0,
                    'e'      => $e->getMessage()
                )
            );
        }
            
        $model = MailBoxModel::model()->byId($messageId)->find();

        $groupId = (int)$model->group_id;

        if (($groupId > 1) && ($groupId < 4)) {
            $this->sendJSON(array('result'=>0));
            return;
        };

        $service = new MailBoxService();
        $characters = $service->getCharacters();

        $subject = $model->subject;
        if ($subject == '') {
            if ($model->subject_id > 0) {
                $subjectModel = MailThemesModel::model()->byId($model->subject_id)->find();
                if ($subjectModel) {
                    $subject = $subjectModel->name;
                }
            }
        }

        $result = array();
        $result['result'] = 1;

        $subject = 'Re: '.$subject;
        # TODO: refactor this. name is not unique
        $subjectModel = MailThemesModel::model()->byName($subject)->find();
        if (!$subjectModel) {
            // добавим тему
            $subjectModel = new MailThemesModel();
            $subjectModel->name = $subject;
            $subjectModel->sim_id = $simId;
            $subjectModel->insert();
        }



        if ($subjectModel) {
            $subjectId = $subjectModel->id;
            $result['subjectId'] = $subjectId;

            $characterThemeModel = MailCharacterThemesModel::model()
                    ->byCharacter($model->sender_id)
                    ->byTheme($subjectId)->find();
            if ($characterThemeModel) {
                $characterThemeId = $characterThemeModel->id;
                if ($characterThemeModel->constructor_number === 'TXT') {
                    $result['phrases']['message'] = $characterThemeModel->letter->message;
                } else {
                    $result['phrases']['data'] = $service->getMailPhrases($characterThemeId);
                }
            }
        }

        if ( !isset($result['phrases'])) {
            $result['phrases']['data'] = $service->getMailPhrases();
        }  // берем дефолтные
        $result['phrases']['addData'] = $service->getSigns();

        $result['receiver'] = $characters[$model->sender_id];
        $result['receiverId'] = $model->sender_id;
        $result['subject'] = $subject;
        $result['subjectId'] = $subjectModel->id;;

        return $this->sendJSON($result);
    }
    
    public function actionReplyAll() {
        try {
            $messageId = (int)Yii::app()->request->getParam('id', false);  
            $sid = Yii::app()->request->getParam('sid', false); 
            try {
               $simId = SessionHelper::getSimIdBySid($sid);
            } catch (CException $e) {
                return $this->sendJSON(
                    array(
                        'result' => 0,
                        'e'      => $e->getMessage()
                    )
                );
            }
            
            $model = MailBoxModel::model()->byId($messageId)->find();
            
            $groupId = (int)$model->group_id;
            
            if (($groupId > 1) && ($groupId < 4)) {
                return $this->sendJSON(array('result'=>0));
            };
            
            $service = new MailBoxService();
            $characters = $service->getCharacters(array(), true);
            
            $subject = $model->subject;
            if ($subject == '') {
                if ($model->subject_id > 0) {
                    $subjectModel = MailThemesModel::model()->byId($model->subject_id)->find();
                    if ($subjectModel) {
                        $subject = $subjectModel->name;
                    }
                }
            }
            
            $result = array();
            $result['result'] = 1;
            
            // Subject {
            
            $subject = 'Re: '.$subject;
            $subjectModel = MailThemesModel::model()->byName($subject)->find();
            if (!$subjectModel) {
                // у нас нет такой темы, значит создадим ее
                $subjectId = MailBoxService::createSubject($subject, $simId);
            }
            
            if ($subjectModel) {
                $subjectId = $subjectModel->id;                

                $characterThemeModel = MailCharacterThemesModel::model()
                        ->byCharacter($model->sender_id)
                        ->byTheme($subjectId)->find();
                if ($characterThemeModel) {
                    $characterThemeId = $characterThemeModel->id;
                    if ($characterThemeModel->constructor_number === 'TXT') {
                        $result['phrases']['message'] = $characterThemeModel->letter->message;
                    } else {
                        $result['phrases']['data'] = $service->getMailPhrases($characterThemeId);
                    }
                }
            }
            
            // Subject }
            
            if (!isset($result['phrases'])) $result['phrases']['data'] = $service->getMailPhrases();  // берем дефолтные
            $result['phrases']['addData'] = $service->getSigns();
            
            $result['subjectId'] = $subjectModel->primaryKey;
            
            $result['receiver'] = $characters[$model->sender_id];
            $result['receiverId'] = $model->sender_id;
            $result['subject'] = $subject;
            
            // добавим копии {
            $copiesIds = array();

            $collection = MailReceiversModel::model()->byMailId($messageId)->findAll();
            
            foreach($collection as $model) {
                if (1 !== (int)$model->receiver_id) {
                    $copiesIds[] = $model->receiver_id;
                }
            }            
            
            if (count($copiesIds) > 0) {
                $copies = $service->getCharacters($copiesIds);
                $result['copies'] = implode(',', $copies);
            }
            else {
                $result['copies'] = '';
            }
            $result['copiesId'] = implode(',', $copiesIds);
            // добавим копии }
            
            return $this->sendJSON($result);
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->sendJSON($result);
        }
    }
    
    /**
     * Получение списка потенциальных задач для добавления в план
     */
    public function actionToPlan() {
        try {
            $messageId = (int)Yii::app()->request->getParam('id', false);  
            if ($messageId == 0) throw new Exception("wrong messageId");
                
            $sid = Yii::app()->request->getParam('sid', false);  
            if (!$sid) throw new Exception("wrong sid");
            
            // определить идентификатор шаблона письма
            $templateId = (int)MailBoxService::getTemplateId($messageId);
            if ($templateId == 0) throw new Exception("cant get template for id : $messageId");
            
            $email = MailBoxModel::model()->findByPk($messageId);
            
            // not planned yet
            if (0 == $email->plan) {
            // получить список задач для шаблона письма
                $tasks = MailBoxService::getTasks($templateId);
                //var_dump($tasks);

                // вернуть результат
                $result = array();
                $result['result'] = 1;
                $result['data'] = $tasks;
            } else {
                // has been planned
                $result = array();
                $result['result'] = 1;
                $result['data'] = array(); 
            }
            
            return $this->sendJSON($result);
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->sendJSON($result);
        }    
    }
    
    /**
     * Добавление задачи в план
     */
    public function actionAddToPlan() {
        try {
            $taskId = (int)Yii::app()->request->getParam('id', false);
            $messageId = (int)Yii::app()->request->getParam('messageId', false);
            $sid = Yii::app()->request->getParam('sid', false);  
            $simId = SessionHelper::getSimIdBySid($sid);
            
            // пределить название задачи
            $model = MailTasksModel::model()->byId($taskId)->find();
            if (!$model) throw new Exception("cant get model by taskId $taskId");
            $name = $model->name;
            if(!empty($messageId)){
                $message = MailBoxModel::model()->byId($messageId)->find();
                $message->plan = 1;
                $message->update();
            }else{
                throw new Exception('messageId не передан или пустой!');
            }
            // Добавить новую задачу в план
            $task = new Task();
            $task->simulation   = $simId;
            $task->title        = $name;
            $task->duration     = $model->duration;
            $task->category     = $model->category;
            TodoService::createTask($task);
            
            TodoService::add($simId, $task->id);
            
            $result = array();
            $result['result'] = 1;
            $result['taskId'] = $task->id;
            return $this->sendJSON($result);
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->sendJSON($result);
        }        
    }
    
    /**
     *  Отправить из черновиков
     */
    public function actionSendDraft() {
        try {
            $mailId = (int)Yii::app()->request->getParam('id', false);  
            $sid = Yii::app()->request->getParam('sid', false);  
            $simId = SessionHelper::getSimIdBySid($sid);
            
            $model = MailBoxModel::model()->byId($mailId)->find();
            if (!$model) throw new CHttpException(200, "cant get model by id $mailId");
            $model->group_id = 3;
            $model->sending_date = time();
            $model->save();
            
            $result = array();
            $result['result'] = 1;
            $this->sendJSON($result);
        } catch (CHttpException $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            $this->sendJSON($result);
        }   
    }
    
    /**
     * Переслать письмо
     * @return type 
     */
    public function actionForward() {
        try {
            $mailId = (int)Yii::app()->request->getParam('id', false);  
            $sid = Yii::app()->request->getParam('sid', false);  
            $simId = SessionHelper::getSimIdBySid($sid);
            
            // получить тему письма
            $model = MailBoxModel::model()->byId($mailId)->find();
            if (!$model) throw new Exception("cant get model by id $mailId");
            $subject = $model->subject;
            $subjectId = $model->subject_id;
            $sender = $model->sender_id;
            $receiverId = $model->receiver_id;

            $subject = 'Fwd: '.$subject; // 'Fwd: ' with space-symbol, 
            // it is extremly important to find proper  Fwd: in database
            
            $subjectId = MailBoxService::getSubjectIdByName($subject);

            // изменить тему и создать новую
            if (false === $subjectId) {
                $subjectId = MailBoxService::createSubject($subject, $simId);
            }
            
            $result = array();
            
            // загрузить фразы по старой теме
            $service = new MailBoxService();
            
            ///////////////////////
            if ($subjectId>0) {
                $characterThemeModel = MailCharacterThemesModel::model()
                        ->byCharacter($receiverId)
                        ->byTheme($subjectId)->find();
                if ($characterThemeModel) {
                    $characterThemeId = $characterThemeModel->id;
                    if ($characterThemeModel->constructor_number === 'TXT') {
                        $result['text'] = $characterThemeModel->letter->message;
                    } else {
                        $result['phrases']['data'] = $service->getMailPhrases($characterThemeId);
                        $result['subjectId'] = $characterThemeId; //$subjectId;
                    }
                }
            }

            if ( !isset($result['phrases']) && !isset($result['text'])) {
                $result['phrases']['data'] = $service->getMailPhrases();
            }  // берем дефолтные
            $result['phrases']['addData'] = $service->getSigns();
            //////////////////////
            
            $result['result'] = 1;
            $result['subject'] = $subject;
            $result['subjectId'] = $subjectId;
            
            return $this->sendJSON($result);
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->sendJSON($result);
        }   
    }
}


