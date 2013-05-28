<?php


class DebugController extends AjaxController
{
    public function actionIndex()
    {
        TestUserHelper::addUser('corporate');
    }
}

