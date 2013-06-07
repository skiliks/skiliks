<?php


class DebugController extends AjaxController
{
    public function actionIndex()
    {
        //TestUserHelper::addUser("personal");
        echo TestUserHelper::getActivationUrl("ivan@skiliks.com");
    }
}

