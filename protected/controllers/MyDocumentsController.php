<?php



/**
 * Контроллер моих документов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MyDocumentsController extends AjaxController{
    
    /**
     * Получение списка документов
     */
    public function actionGetList() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("empty sid");
            
            $simId = SessionHelper::getSimIdBySid($sid);
            
            $documents = MyDocumentsModel::model()->bySimulation($simId)->findAll();
            $list = array();
            foreach($documents as $document) {
                $list[] = array(
                    'id' => $document->id,
                    'name' => $document->fileName
                );
            }
            
            $result = array();
            $result['result'] = 1;
            $result['data'] = $list;
            
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array('result' => 0, 'message' => $exc->getMessage());
            return $this->_sendResponse(200, CJSON::encode($result));
        }
    }
}

?>
