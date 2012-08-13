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
        $sql = "SELECT COUNT( * ) AS count, group_id
                FROM  `mail_box` 
                WHERE sim_id = :simId AND readed = 0
                GROUP BY group_id";
        
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $data = $command->queryAll();

        foreach($data as $row) { 
            $folders[$row['group_id']]['unreaded'] = $row['count'];
        }
        
        //var_dump($folders);  die();
        
        $result = array();
        $result['result'] = 1;
        $result['folders'] = $folders;
        $result['messages'] = $service->getMessages(array(
            'folderId' => $folders[1]['id'],
            'receiverId' => $receiverId
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
        $orderType = (int)Yii::app()->request->getParam('orderType', false);  
        
        $receiverId = SessionHelper::getUidBySid($sid);
        
        $service = new MailBoxService();
        
        $result = array();
        $result['result'] = 1;
        $result['messages'] = $service->getMessages(array(
            'folderId' => $folderId,
            'receiverId' => $receiverId,
            'order' => $order,
            'orderType' => $orderType,
            'uid' => $receiverId
        ));
        
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
        $service = new MailBoxService();
        $service->delete($id);
        
        $result = array();
        $result['result'] = 1;
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionMarkRead() {
        $id = (int)Yii::app()->request->getParam('id', false);  
        $sid = Yii::app()->request->getParam('sid', false);  
        $simId = SessionHelper::getSimIdBySid($sid);
        
        $service = new MailBoxService();
        $service->setAsReaded($id);
        
        // получить колличество непрочитанных сообщений
        $model = MailBoxModel::model()->byId($id)->find();
        $folderId = (int)$model->group_id;
        
        
        // добавляем информацию о колличестве непрочитанных сообщений в подпапках
        $sql = "SELECT COUNT( * ) AS count
                FROM  `mail_box` 
                WHERE sim_id = :simId AND readed = 0 and group_id = :groupId";
        
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $command->bindParam(":groupId", $folderId, PDO::PARAM_INT);
        $row = $command->queryRow();
        
        //var_dump($row); die();
        
        
        $result = array();
        $result['result'] = 1;
        $result['folderId'] = $folderId;
        $result['unreaded'] = $row['count'];
        
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    /**
     * Перенести письмо в другую папку
     */
    public function actionMove() {
        try {
            $messageId = (int)Yii::app()->request->getParam('messageId', false);  
            $folderId = (int)Yii::app()->request->getParam('folderId', false);  
        
            $model = MailBoxModel::model()->byId($messageId)->find();
            if (!$model) {
                throw new Exception("cant find model by id : {$messageId}");
            }
            
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
            }
            
            $model->group_id = $folderId;
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
}

?>
