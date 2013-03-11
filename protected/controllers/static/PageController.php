<?php

class PageController extends AjaxController
{
    public $user;
    public $signInErrors = [];

    public function actionTeam()
    {
        $user_id = Yii::app()->session['uid'];
        $this->user = YumUser::model()->findByPk($user_id);

        $this->render('team');
    }
}
