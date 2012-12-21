<?php

class ImportController extends AjaxController
{

    public function actionIndex()
    {
        $controllers = array('MailImportController', 
                             'CharactersPointsTitleImportController',
                             'DialogImportController',
                             'MailImportController', 
                             'MyDocumentsImportController', 
                             'TasksImportController');
        $links = System::classToUrls($controllers);
        //var_dump($links);
        $this->render('index', array('links'=>$links));
    }
}