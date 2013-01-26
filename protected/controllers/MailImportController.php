<?php

/**
 * Импорт почты
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailImportController extends AjaxController{
    
    public function actionImport()
    {        
        $importService = new ImportGameDataService();
        $result = $importService->importEmails();    	

        $this->_sendResponse(200, $result['text'], 'text/html');
    }
    
    /**
     * Импорт задач для писем M-T
     */
    public function actionImportTasks() 
    {
        $importService = new ImportGameDataService();
        $result = $importService->importMailTasks();

        $this->_sendResponse(200, $result['text'], 'text/html'); 
    }
    
    /**
     * Импорт фраз для писем
     */
    public function actionImportPhrases() {
        
        $import = new ImportMailPhrases();
        $result = $import->run();       
        
        $this->_sendResponse(200, $result['text'], 'text/html');
    }
    
    /**
     * Импорт тем для писем F-S-C
     */
    public function actionImportThemes() 
    {
    	$importService = new ImportGameDataService();
        $result = $importService->importMailEvents();   	

        $this->_sendResponse(200, $result['text'], 'text/html');
    }
    
    /**
     * Импорт событий из писем
     */
    public function actionImportEvents() 
    {
        $importService = new ImportGameDataService();
        $result = $importService->importEmailSubjects();    	

        $this->_sendResponse(200, $result['text'], 'text/html');   
    }
    
    public function actionImportTime() 
    {
        $importService = new ImportGameDataService();
        $result = $importService->importMailSendingTime();	

        $this->_sendResponse(200, $result['text'], 'text/html'); 
    }
    
    public function actionImportAttache() 
    {
        $importService = new ImportGameDataService();
        $result = $importService->importMailAttaches();

        $this->_sendResponse(200, $result['text'], 'text/html');      
    }

}


