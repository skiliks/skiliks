<?php

class ActivityImportController extends AjaxController
{
    public function actionIndex()
    {
        $import = new ImportGameDataService();
        $start_time = microtime(true);
        $result = $import->importActivity();
        $end_time = microtime(true);
        $result['time'] = $end_time - $start_time;
        $this->render('index', $result);
    }
}