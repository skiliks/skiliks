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
        
        $service = new MailBoxService();
        $folders = $service->getFolders();
        
        $result = array();
        $result['result'] = 1;
        $result['folders'] = $folders;
        $result['messages'] = $service->getMessages(array(
            'folderId' => $folders[0]['id'],
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
            'orderType' => $orderType
        ));
        
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionGetMessage() {
        $id = (int)Yii::app()->request->getParam('id', false);  
        
        $service = new MailBoxService();
        $message = $service->getMessage($id);
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
        
        $folder = (int)Yii::app()->request->getParam('folder', false);  
        $receiver = (int)Yii::app()->request->getParam('receiver', false);  
        $receivers = Yii::app()->request->getParam('receivers', false);  
        $subject = Yii::app()->request->getParam('subject', false);  
        $message = Yii::app()->request->getParam('message', false);  
        
        $service = new MailBoxService();
        $service->sendMessage(array(
            'group' => $folder,
            'sender' => $senderId,
            'receiver' => $receiver,
            'subject' => $subject,
            'message' => $message
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
}

?>
