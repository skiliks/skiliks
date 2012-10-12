<?php



/**
 * Description of ViewerController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ViewerController extends AjaxController{
    
    /**
     * Получение файла
     */
    public function actionGet() {
        try {
            $fileId = (int)Yii::app()->request->getParam('fileId', false);
            
            // получить шаблон файла
            $templateId = MyDocumentsService::getTemplate($fileId);
            if (!$templateId)  throw new Exception("немогу определить шаблон для файла {$fileId}");
            
            $items = ViewerTemplateModel::model()->byFile($templateId)->findAll();
            $list = array();
            $fileId = 0;
            foreach($items as $item) {
                $fileId = $item->file_id;
                $list[] = $item->filePath;
            }
            
            // получим кода файлов
            $file = MyDocumentsTemplateModel::model()->byId($fileId)->find();
            
            foreach($list as $index=>$item) {
                $list[$index] = $file->code.'/'.$item;
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
