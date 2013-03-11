<?php
class ProductController extends AjaxController
{
    public $user;
    public $signInErrors = [];

    public function actionIndex()
    {
        $user_id = Yii::app()->session['uid'];
        $user = YumUser::model()->findByPk($user_id);

        $this->render('index');
    }
}


