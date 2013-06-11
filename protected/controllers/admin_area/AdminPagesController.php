<?php

class AdminPagesController extends AjaxController {

    public function actionDashboard() {

        $this->layout = 'admin_area';

        $this->render('/admin_area/admin_pages/dashboard');

    }

}