<?php

class PagesController extends AjaxController
{
    public $user;
    public $signInErrors = [];

    public function actionIndex()
    {
        // this page currently will be just RU
        if (null === Yii::app()->request->getParam('_lang') && null === Yii::app()->language) {
            Yii::app()->language = 'ru';
        }

        $this->render('home', [
            'assetsUrl'      => $this->getAssetsUrl(),
            'userSubscribed' => false,
        ]);
    }

    /**
     *
     */
    public function actionTeam()
    {
        var_dump(Yii::app()->language);

        $user_id = Yii::app()->session['uid'];
        $this->user = YumUser::model()->findByPk($user_id);

        $this->render('team');
    }

    /**
     *
     */
    public function actionProduct()
    {
        $user_id = Yii::app()->session['uid'];
        $user = YumUser::model()->findByPk($user_id);

        $this->render('product');
    }

    /**
     *
     */
    public function actionComingSoonSuccess()
    {
        $this->render('home', [
            'assetsUrl'      => $this->getAssetsUrl(),
            'userSubscribed' => true,
        ]);
    }

    /**
     *
     */
    public function actionAddUserSubscription()
    {
        $email = Yii::app()->request->getParam('email', false);
        $result = UserService::addUserSubscription($email);

        $this->sendJSON($result);
    }
}
