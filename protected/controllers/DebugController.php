<?php


class DebugController extends AjaxController
{
    /**
     *
     */
    public function actionIndex()
    {
        //TestUserHelper::addUser("personal");
        echo "TEST";
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

    public function actionStyleGrid()
    {
        $this->layout = false;
        $this->render('style_grid');
    }

    public function actionStyleGridResults()
    {
        $this->layout = false;
        $this->render('style_grid_results');
    }

    public function actionStyleBlocks()
    {
        $this->layout = false;
        $this->render('style_blocks');
    }

    public function actionStyleEmpty1280()
    {
        $this->layout = false;
        $this->render('style_empty_1280');
    }
}

