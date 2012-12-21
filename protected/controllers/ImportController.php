<?php

class ImportController extends AjaxController
{

    public function actionIndex()
    {
        $controllers = array('MailImportController', 
                             'CharactersPointsTitleImportController',
                             'DialogImportController',
                             'MyDocumentsImportController', 
                             'TasksImportController');
        $links = System::classToUrls($controllers);
        //var_dump($links);
        $this->render('index', array('links'=>$links));
    }
}