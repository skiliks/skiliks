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
        $folders = MailFoldersModel::model()->findAll();
        
        $result = array();
        $result['result'] = 1;
        foreach($folders as $folder) {
            $result['data'][] = array(
                'id' => $folder->id,
                'name' => $folder->name
            );
        }
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    /**
     * Получение списка сообщений
     */
    public function actionGetMessages() {
        $folderId = (int)Yii::app()->request->getParam('folderId', false);  
        $receiverId = (int)Yii::app()->request->getParam('receiverId', false);  
        
        $messages = MailBoxModel::model()->byReceiver($receiverId)->byFolder($folderId)->findAll();
        
        $result = array();
        $result['result'] = 1;
        
        $list = array();
        foreach($messages as $message) {
            $list[] = array(
                'id' => $message->id,
                'subject' => $message->subject,
                'message' => $message->message,
                'sendingDate' => $message->sending_date,
                'receivingDate' => $message->receiving_date,
                'sender' => $message->sender_id,
                'receiver' => $message->receiver_id
            );
            
        }
        
        $result['data'] = $list;
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionGetReceivers() {
        
    }
    
    public function actionSave() {
        
    }
}

?>
