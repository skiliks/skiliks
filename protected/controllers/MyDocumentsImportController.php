<?php


/**
 * Контроллер импорта документов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MyDocumentsImportController extends AjaxController{
    
    /**
     * Импорт документов
     */
    public function actionImport() 
    {
        $importService = new ImportGameDataService();
        $result = $importService->importMyDocuments();

        $this->_sendResponse(200, $result['text']);
    }
}


