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

        $this->renderText($result['text']);
    }
}


