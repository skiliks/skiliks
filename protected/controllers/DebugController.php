<?php


class DebugController extends AjaxController
{
    /**
     *
     */
    public function actionIndex()
    {
        //TestUserHelper::addUser("personal");
        echo TestUserHelper::getActivationUrl("ivan@skiliks.com");
    }

    /**
     *
     */
    public function actionStyleCss()
    {
        $this->layout = false;
        $this->render('style_css');
    }

    /**
     *
     */
    public function actionStyleForPopupCss()
    {
        $this->layout = false;
        $this->render('style_for_popup_css');
    }
}

