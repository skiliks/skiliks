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

        $this->renderText($result['text']);
    }
    
    /**
     * Импорт задач для писем M-T
     */
    public function actionImportTasks() 
    {
        $importService = new ImportGameDataService();
        $result = $importService->importMailTasks();

        $this->renderText($result['text']); 
    }
    
    /**
     * Импорт фраз для писем
     */
    public function actionImportPhrases() {
        
        $import = new ImportMailPhrases();
        $result = $import->run();       
        
        $this->renderText($result['text']);
    }
    
    /**
     * Импорт тем для писем F-S-C
     */
    public function actionImportThemes() 
    {
    	$importService = new ImportGameDataService();
        $result = $importService->importMailEvents();   	

        $this->renderText($result['text']);
    }
    
    /**
     * Импорт событий из писем
     */
    public function actionImportEvents() 
    {
        $importService = new ImportGameDataService();
        $result = $importService->importEmailSubjects();    	

        $this->renderText($result['text']);   
    }
    
    public function actionImportTime() 
    {
        $importService = new ImportGameDataService();
        $result = $importService->importMailSendingTime();	

        $this->renderText($result['text']); 
    }
    
    public function actionImportAttache() 
    {
        $importService = new ImportGameDataService();
        $result = $importService->importMailAttache();

        $this->renderText($result['text']);      
    }

}


