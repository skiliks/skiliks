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
            $files = array();
            foreach($items as $item) {
                $files[] = $item->file_id;
                $list[$item->file_id] = $item->filePath;
            }
            
            // получим кода файлов
            $fileColection = MyDocumentsTemplateModel::model()->byIds($files)->findAll();
            foreach($fileColection as $file) {
                $list[$file->id] = $file->code.'/'.$list[$file->id];
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
