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
        Logger::debug('scenario upload called');
        
        $result = 0; $processed = 0;
        try {
  
            // загружаем файл
            $fileName = UploadHelper::uploadSimple();
            Logger::debug('uploaded file : '.$fileName);

            if (!$fileName) throw new Exception ('Не могу загрузить файл');

            // импорт файла
            $service = new DialogImportService();
            $processed = $service->import($fileName);
            Logger::debug('processed records : '.$processed);

            $html = "<script language=\"javascript\" type=\"text/javascript\">
                        alert('Импорт завершен. Обработано записей {$processed}');
                    </script>";
            return $this->_sendResponse(200, $html, 'text/html');

            //$result['message'] = "Обработано записей {$processed}";
        } catch (Exception $exc) {
            $result = 0;
        }

        $html = "<script language=\"javascript\" type=\"text/javascript\">
                        alert('Что-то пошло не так');
                    </script>";
        
        return $this->_sendResponse(200, $html, 'text/html');
        
        //Logger::debug('file : '.$fileName);
        
        //$result = array('result' => 1, 'message' => 'file : '.$fileName);
        return $this->_sendResponse(200, CJSON::encode($result));
    }
}

?>
