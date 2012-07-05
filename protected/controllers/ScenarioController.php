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
        Logger::debug('file : '.$fileName);
        
        return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'message' => 'i was called')));
    }
}

?>
