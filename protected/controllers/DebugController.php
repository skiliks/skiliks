<?php


class DebugController extends AjaxController
{
    public function actionIndex()
    {
        //1367911963
        $datetime = new DateTime('now', new DateTimeZone('Europe/Moscow'));
        $datetime->setTimestamp(1367911963);
        echo $datetime->format("H:i");
    }
}

