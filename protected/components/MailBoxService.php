<?php



/**
 * Description of MailBoxService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailBoxService {
    
    /**
     * Получить список папок
     * @return type 
     */
    public function getFolders() {
        $folders = MailFoldersModel::model()->findAll();
        
        $list = array();
        foreach($folders as $folder) {
            $list[] = array(
                'id' => $folder->id,
                'name' => $folder->name
            );
        }
        return $list;
    }
    
    /**
     * Получение списка собщений
     * @param int $folderId
     * @param int $receiverId
     * @return array
     */
    public function getMessages($params) {
        
        $folderId = $params['folderId'];
        $receiverId = $params['receiverId'];
        $order = (isset($params['order'])) ? $params['order'] : false;
        if ($order == 'sender') $order = 'sender_id';
        if ($order == 'time') $order = 'receiving_date';
        
        $orderType = (isset($params['orderType'])) ? $params['orderType'] : false;
        if ($orderType == 0) $orderType = 'ASC';
        else $orderType = 'DESC';
        
        $model = MailBoxModel::model();
        $model->byReceiver($receiverId)->byFolder($folderId);
        if ($order) $model->orderBy($order, $orderType);
        $messages = $model->findAll();
        
       
        $users = array();
        $list = array();
        foreach($messages as $message) {
            $senderId = (int)$message->sender_id;
            $receiverId = (int)$message->receiver_id;
            $users[$senderId] = $senderId;
            $users[$receiverId] = $receiverId;
            
            $list[] = array(
                'id' => $message->id,
                'subject' => $message->subject,
                'message' => $message->message,
                'sendingDate' => DateHelper::toString($message->sending_date),
                'receivingDate' => DateHelper::toString($message->receiving_date),
                'sender' => $senderId,
                'receiver' => $message->receiver_id
            );
            
        }
        
        $charactersCollection = Characters::model()->byIds($users)->findAll();
        $characters = array();
        foreach($charactersCollection as $characterModel) {
            $characters[$characterModel->id] = $characterModel->fio.' <'.$characterModel->email.'>';
        }

        foreach($list as $index=>$item) {
            $list[$index]['sender'] = $characters[$list[$index]['sender']];
            $list[$index]['receiver'] = $characters[$list[$index]['receiver']];
        }
        
        return $list;
    }
}

?>
