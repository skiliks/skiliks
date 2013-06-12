<?php

class AdminPagesController extends AjaxController {

    public function actionDashboard() {

        $this->layout = 'admin_area';
        Yii::app()->user->setFlash('error', "Data saved!");
        $this->render('/admin_area/admin_pages/dashboard');

    }

}