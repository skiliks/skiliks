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
    
}