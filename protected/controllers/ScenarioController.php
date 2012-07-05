<?php


/**
 * Контроллер загрузки сценария
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ScenarioController extends AjaxController{
 
    /**
     * Загрузка сценария
     * @return type 
     */
    public function actionUpload() {
        Logger::debug('i was called');
        
        // загружаем файл
        $fileName = UploadHelper::upload();
        
        if (!$fileName) {
            $result = array('result' => 0, 'message' => 'Не могу загрузить файл');
            return $this->_sendResponse(200, CJSON::encode($result));
        }
        
        // импорт файла
        $result = array('result' => 0);
        $service = new DialogImportService();
        try {
            $processed = $service->import($fileName);
            /*if ($processed == 0) {
                
            }*/
            $result['result'] = 1;
            $result['message'] = "Обработано записей {$processed}";
        } catch (Exception $exc) {
            $result['message'] = $exc->getMessage();
        }

        
        
        //Logger::debug('file : '.$fileName);
        
        //$result = array('result' => 1, 'message' => 'file : '.$fileName);
        return $this->_sendResponse(200, CJSON::encode($result));
    }
}

?>
