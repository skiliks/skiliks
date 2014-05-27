<?php

class BaseAdminController extends SiteBaseController {

    /**
     * @param CAction $action
     * @return bool
     */
    public function beforeAction($action) {

        $public = ['Login'];
        $user = Yii::app()->user->data();
        $this->user = $user;
        if (in_array($action->id, $public)) {
            parent::beforeAction($action);
            return true;
        } elseif (!$user->isAuth()) {
            $this->redirect('/admin_area/login');
        } elseif (!$user->isAdmin()) {
            $this->redirect('/dashboard');
        }
        parent::beforeAction($action);

        return true;
    }
} 