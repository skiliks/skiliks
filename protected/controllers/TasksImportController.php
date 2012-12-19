<?php
/**
 * Description of TasksImportController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class TasksImportController extends AjaxController
{
    public function actionImport()
    {
        $importService = new ImportGameDataService();
        $result = $importService->importTasks();    	

        $this->renderText($result['text']);    
    }    
}


