<?php
/**
 * Контроллер документа Excel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelDocumentController extends AjaxController
{
    /**
     * New code!
     * @autor Slavka
     * @return 
     */
    public function actionGet() 
    {
        try {
            $sid = Yii::app()->request->getParam('sid', false);  
            if (!$sid) throw new Exception('wrong sid');
            SessionHelper::setSid($sid);
            
            $fileId = (int)Yii::app()->request->getParam('fileId', false);  
            
            $simId = SessionHelper::getSimIdBySid($sid);
            if (!$simId) throw new Exception("Can`t find simId by sid {$sid}");
            
            if ($fileId == 0) {
                throw new Exception("Can`t find file by id {$fileId}");
            }

            $result = ExcelFactory::getDocument()->loadByFile($simId, $fileId)->populateFrontendResult($simId, $fileId);
            
            $result['fileId'] = $fileId;
            
            $this->sendJSON($result);
        } catch (Exception $exc) {
            $this->sendJSON(array(
                'result' => 0,
                'filedId' => $fileId,
                'excelDocumentUrl' => '/pages/excel/fileNotFound.html',
                'message' => $exc->getMessage(),
                'code' => $exc->getCode()
            ));
        }
    }

    /**
     * New code!
     */
    public function actionGetExcelID() {
        $fileId = Yii::app()->request->getParam('fileId', false);
        $uid = SessionHelper::getUidBySid(); // получаем uid

        try {
            $sim_id = SessionHelper::getSimIdBySid($uid);
        } catch(CException $e) {
            $this->sendJSON(null);
        }

        $res = array();
        if(empty($fileId) OR $fileId === "null"){
            $res['id'] = $this->_getFileID($sim_id);
            $res['time'] = $this->_getFileTime($sim_id, $res['id']);
        }else{
            $res['time'] = $this->_getFileTime($sim_id, $fileId);
        }
        
        $this->sendJSON($res);
    }
    
    /**
     * New code!
     * @param type $sim_id
     * @return type
     * @throws Exception
     */
    private function _getFileID($sim_id) {
            $id = Yii::app()
            ->db
            ->createCommand()
            ->select('id')
            ->from('my_documents')
            ->where("sim_id = :sim_id AND template_id = 33", array(":sim_id"=>$sim_id))    
            ->queryRow();
            if(empty($id['id'])){
                throw new Exception("файл не может быть не задан для симуляции - {$sim_id}!");
            }else{
                return $id['id'];
            }
    }
    
    /**
     * New code!
     * @param type $sim_id
     * @param type $fileId
     * @return null
     * @throws Exception
     */
    private function _getFileTime($sim_id, $fileId) {
        $file = $_SERVER['DOCUMENT_ROOT'].'/documents/'.$sim_id.'/'.$fileId.'.xls';
        if(file_exists($file)){
            $time = filemtime($file);
            if($time !== false){
                return $time;
            } else {
                throw new Exception('Ошибка с файлом '.$file);
            }
        }else{
            return null;
        }
    }
}



