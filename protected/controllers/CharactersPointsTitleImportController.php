<?php

/**
 * Импорт 
 *
 * @author Ivan Tugay <listepo@ya.ru>
 */
class CharactersPointsTitleImportController extends AjaxController {
    
    public function actionImport() 
    {
    	$importService = new ImportGameDataService();
        $result = $importService->importCharactersPointsTitles();    	

        $this->renderText($result['text']);
    }
    
	public function actionLogdialog() {
		LogHelper::getDialogDetail(LogHelper::RETURN_CSV);
	}
        
    public function actionTest() {

        var_dump(LogHelper::ACTION_ACTIVATED == "0");
   
            
	}
        
    public function actionLogDialogAvg() {
            
       LogHelper::getDialogAggregate(LogHelper::RETURN_CSV);
            
    }
    
    public function actionLogDocuments() {
            
        LogHelper::getDocuments(LogHelper::RETURN_CSV);
            
    }

    public function actionMailInBox() {

        LogHelper::getMailInDetail(LogHelper::RETURN_CSV);

    }

    public function actionMailInBoxAVG() {

        LogHelper::getMailInAggregate(LogHelper::RETURN_CSV);

    }
    
    public function actionMailOutBox() {

        LogHelper::getMailOutDetail(LogHelper::RETURN_CSV);

    }

    public function actionMailOutAVG() {

        LogHelper::getMailOutAggregate(LogHelper::RETURN_CSV);

    }
    
    public function actionWindowsLog() {

        LogHelper::getWindows(LogHelper::RETURN_CSV);

    }
    
}