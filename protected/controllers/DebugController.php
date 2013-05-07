<?php


class DebugController extends AjaxController
{
    public function actionIndex()
    {
        $datetime = new DateTime('now', new DateTimeZone('Europe/Moscow'));
        echo $datetime->format('H:i');
    }
}

