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
    
    public function actionGetMessages() {
        
    }
    
    public function actionGetReceivers() {
        
    }
    
    public function actionSave() {
        
    }
}

?>
