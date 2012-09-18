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
        $receiverId = SessionHelper::getUidBySid($sid);
        $simId = SessionHelper::getSimIdBySid($sid);
        
        $service = new MailBoxService();
        $folders = $service->getFolders();
        
        
        // добавляем информацию о колличестве непрочитанных сообщений в подпапках
        $unreadInfo = MailBoxService::getFoldersUnreadCount($simId);
        foreach($unreadInfo as $folderId => $count) {
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
        
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    /**
     * Получение списка сообщений
     */
    public function actionGetMessages() {
        $sid = Yii::app()->request->getParam('sid', false);  
        $folderId = (int)Yii::app()->request->getParam('folderId', false);  
        $order = Yii::app()->request->getParam('order', false);  
        $orderType = (int)Yii::app()->request->getParam('order_type', false);  
        
        $receiverId = SessionHelper::getUidBySid($sid);
        $simId = SessionHelper::getSimIdBySid($sid);
        
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
        
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionGetMessage() {
        $id = (int)Yii::app()->request->getParam('id', false);  
        
        $service = new MailBoxService();
        $message = $service->getMessage($id);
        //$service->setAsReaded($id);
        //var_dump($message);
        $result = array();
        $result['result'] = 1;
        $result['data'] = $message;
        return $this->_sendResponse(200, CJSON::encode($result));
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
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionGetPhrases() {
        $id = (int)Yii::app()->request->getParam('id', false);  
        
        $service = new MailBoxService();
        
        $result = array();
        $result['result'] = 1;
        $result['data'] = $service->getMailPhrases($id);
        $result['addData'] = $service->getSigns();
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionSendMessage() {
        $sid = Yii::app()->request->getParam('sid', false);  
        $senderId = SessionHelper::getUidBySid($sid);
        $simId = SessionHelper::getSimIdBySid($sid);
        
        $folder = 3; //(int)Yii::app()->request->getParam('folder', false);  
        //$receiver = (int)Yii::app()->request->getParam('receiver', false);  
        $receivers = Yii::app()->request->getParam('receivers', false);  
        $copies = Yii::app()->request->getParam('copies', false);  
        $subject = (int)Yii::app()->request->getParam('subject', false);  
        
        $phrases = Yii::app()->request->getParam('phrases', false);  
        
        $letterType = Yii::app()->request->getParam('letterType', false);  
        $fileId = (int)Yii::app()->request->getParam('fileId', false);  
        
        //$message = Yii::app()->request->getParam('message', false);  
        
        $service = new MailBoxService();
        $service->sendMessage(array(
            'group' => $folder,
            'sender' => 1, //$senderId, //- отправитель теперь всегда герой
            'receivers' => $receivers,
            'copies' => $copies,
            'subject' => $subject,
            'phrases' => $phrases,
            'simId' => $simId,
            'letterType' => $letterType,
            'fileId' => $fileId
        ));
        
        $result = array();
        $result['result'] = 1;
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionSaveDraft() {
        $sid = Yii::app()->request->getParam('sid', false);  
        $senderId = SessionHelper::getUidBySid($sid);
        $simId = SessionHelper::getSimIdBySid($sid);
        
        $folder = 2; 
        //$receiver = (int)Yii::app()->request->getParam('receiver', false);  
        $receivers = Yii::app()->request->getParam('receivers', false);  
        $copies = Yii::app()->request->getParam('copies', false);  
        $subject = (int)Yii::app()->request->getParam('subject', false);  
        $phrases = Yii::app()->request->getParam('phrases', false);  
        
        //$message = Yii::app()->request->getParam('message', false);  
        
        $service = new MailBoxService();
        $service->sendMessage(array(
            'group' => $folder,
            'sender' => $senderId,
            'receivers' => $receivers,
            'copies' => $copies,
            'subject' => $subject,
            'phrases' => $phrases,
            'simId' => $simId
        ));
        
        $result = array();
        $result['result'] = 1;
        return $this->_sendResponse(200, CJSON::encode($result));
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
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->_sendResponse(200, CJSON::encode($result));
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
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->_sendResponse(200, CJSON::encode($result));
        }    
    }
    
    /**
     * Получение тем
     */
    public function actionGetThemes() {
        $receivers = Yii::app()->request->getParam('receivers', false);  
        
        $service = new MailBoxService();
        
        $result = array();
        $result['result'] = 1;
        $result['data'] = $service->getThemes($receivers);
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionDelete() {
        $id = (int)Yii::app()->request->getParam('id', false);  
        $sid = Yii::app()->request->getParam('sid', false);  
        $simId = SessionHelper::getSimIdBySid($sid);
        
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
        
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionMarkRead() {
        $id = (int)Yii::app()->request->getParam('id', false);  
        $sid = Yii::app()->request->getParam('sid', false);  
        $simId = SessionHelper::getSimIdBySid($sid);
        
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
        
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    /**
     * Перенести письмо в другую папку
     */
    public function actionMove() {
        try {
            $messageId = (int)Yii::app()->request->getParam('messageId', false);  
            $folderId = (int)Yii::app()->request->getParam('folderId', false);  
            $sid = Yii::app()->request->getParam('sid', false);  
            $simId = SessionHelper::getSimIdBySid($sid);
        
            
            $model = MailBoxModel::model()->byId($messageId)->find();
            if (!$model) {
                throw new Exception("cant find model by id : {$messageId}");
            }
            
            
            
            if ($model->group_id > 1 && $model->group_id < 4) {
                return $this->_sendResponse(200, CJSON::encode(array('result'=>0)));
            }
            
            if ($folderId > 1 && $folderId < 4) {
                return $this->_sendResponse(200, CJSON::encode(array('result'=>0)));
            }
            
            /*
            // проверка, а можем ли мы это письмо перемещать
            if ($model->group_id == 1) {
                // из входящих можно перемещать только в корзину
                if ($folderId != 4) {
                    return $this->_sendResponse(200, CJSON::encode(array('result'=>0)));
                }
            }
            if ($model->group_id == 4) {
                // из корзины можно перемещать только во входящие
                if ($folderId != 1) {
                    return $this->_sendResponse(200, CJSON::encode(array('result'=>0)));
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
        
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->_sendResponse(200, CJSON::encode($result));
        }
    }
    
    public function actionReply() {
        try {
            $messageId = (int)Yii::app()->request->getParam('id', false);  
            $sid = Yii::app()->request->getParam('sid', false);  
            $simId = SessionHelper::getSimIdBySid($sid);
            
            $model = MailBoxModel::model()->byId($messageId)->find();
            
            $groupId = (int)$model->group_id;
            
            if (($groupId > 1) && ($groupId < 4)) {
                return $this->_sendResponse(200, CJSON::encode(array('result'=>0)));
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
            
            $subject = 'Re:'.$subject;
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
                    $result['phrases'] = $service->getMailPhrases($characterThemeId);
                    $result['subjectId'] = $characterThemeId; //$subjectId;
                }
            }
            
            
            $result['receiver'] = $characters[$model->sender_id];
            $result['receiverId'] = $model->sender_id;
            $result['subject'] = $subject;
                  
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->_sendResponse(200, CJSON::encode($result));
        }
    }
    
    public function actionReplyAll() {
        try {
            $messageId = (int)Yii::app()->request->getParam('id', false);  
            $sid = Yii::app()->request->getParam('sid', false);  
            $simId = SessionHelper::getSimIdBySid($sid);
            
            $model = MailBoxModel::model()->byId($messageId)->find();
            
            $groupId = (int)$model->group_id;
            
            if (($groupId > 1) && ($groupId < 4)) {
                return $this->_sendResponse(200, CJSON::encode(array('result'=>0)));
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
            
            $subject = 'Re:'.$subject;
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
                    $result['phrases'] = $service->getMailPhrases($characterThemeId);
                    //$result['subjectId'] = $characterThemeId; //$subjectId;
                }
            }
            
            $result['subjectId'] = $subjectId;
            
            $result['receiver'] = $characters[$model->sender_id];
            $result['receiverId'] = $model->sender_id;
            $result['subject'] = $subject;
            
            // добавим копии
            $copiesIds = array();
            $collection = MailCopiesModel::model()->byMailId($messageId)->findAll();
            foreach($collection as $model) {
                $copiesIds[] = $model->receiver_id;
            }
            
            if (count($copiesIds) > 0) {
                $copies = $service->getCharacters($copiesIds);
                $result['copies'] = implode(',', $copies);
            }
            else {
                $result['copies'] = '';
            }
            $result['copiesId'] = implode(',', $copiesIds);
            
                  
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->_sendResponse(200, CJSON::encode($result));
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
            if ($templateId == 0) throw new Exception("cant get template for id : $mailId");
            
            // получить список задач для шаблона письма
            $tasks = MailBoxService::getTasks($templateId);
            //var_dump($tasks);
            
            // вернуть результат
            $result = array();
            $result['result'] = 1;
            $result['data'] = $tasks;
        return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->_sendResponse(200, CJSON::encode($result));
        }    
    }
    
    /**
     * Добавление задачи в план
     */
    public function actionAddToPlan() {
        try {
            $taskId = (int)Yii::app()->request->getParam('id', false);  
            $sid = Yii::app()->request->getParam('sid', false);  
            $simId = SessionHelper::getSimIdBySid($sid);
            
            // пределить название задачи
            $model = MailTasksModel::model()->byId($taskId)->find();
            if (!$model) throw new Exception("cant get model by taskId $taskId");
            $name = $model->name;
            
            // Добавить новую задачу в план
            $task = new Task();
            $task->simulation = $simId;
            $task->title = $name;
            TodoService::createTask($task);
            
            TodoService::add($simId, $task->id);
            
            $result = array();
            $result['result'] = 1;
            $result['taskId'] = $task->id;
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->_sendResponse(200, CJSON::encode($result));
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
            if (!$model) throw new Exception("cant get model by id $mailId");
            $model->group_id = 3;
            $model->sending_date = time();
            $model->save();
            
            $result = array();
            $result['result'] = 1;
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->_sendResponse(200, CJSON::encode($result));
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
            
            if ($subjectId > 0) {
                $subject = MailBoxService::getSubjectById($subjectId);
            }
            else {
                $subjectId = MailBoxService::getSubjectIdByName($subject);
            }
            //var_dump($subject); die();
            // изменить тему и создать новую
            $subject = 'Fwd:'.$subject;
            $newSubjectId = MailBoxService::createSubject($subject, $simId);
            
            // загрузить фразы по старой теме
            $service = new MailBoxService();
            $phrases = $service->getMailPhrasesByCharacterAndTheme($sender, $subjectId);  //$subjectId
            
            $result = array();
            $result['result'] = 1;
            $result['subject'] = $subject;
            $result['subjectId'] = $newSubjectId;
            $result['phrases'] = $phrases;
            
            
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->_sendResponse(200, CJSON::encode($result));
        }   
    }
}

?>
