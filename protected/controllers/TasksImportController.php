<?php
/**
 * Description of TasksImportController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class TasksImportController extends AjaxController
{
    /**
     * @return HttpResponce
     */
    public function actionImport()
    {
        $importService = new ImportGameDataService();
        $result = $importService->importTasks();    	

        $this->_sendResponse(200, $result['text']);    
    }    
}


