<?php

class ImportController extends AjaxController
{

    public function actionIndex()
    {
        $links = [['href' => $this->createUrl('do'), 'title' => 'Начать импорт']];
        $this->render('index', array('links'=>$links));
    }

    public function actionDo()
    {
        $service = new ImportGameDataService();
        $result = $service->importAll();
        $this->render('do', array('result'=>$result));

    }
}