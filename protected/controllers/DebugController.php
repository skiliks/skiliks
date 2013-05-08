<?php


class DebugController extends AjaxController
{
    public function actionIndex()
    {
        //1367911963
       echo strtotime('10:02:10') - strtotime('10:02:00');
    }
}

