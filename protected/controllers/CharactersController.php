<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CharactersController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersController extends AjaxController
{
    /**
     * Import characters
     */
    public function actionImport() 
    {
        $importService = new ImportGameDataService();
        
        $result = $importService->importCaracters();
        
        $this->renderText($result['text']);
    }
}


