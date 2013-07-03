<?php

class SiteBaseController extends CController {

    /**
     * @var $user YumUser
     */
    public $user;

    /**
     * @param CAction $action
     * @return bool
     */
    protected function beforeAction($action)
    {
        $allowed = Yii::app()->params['allowedLanguages'];
        $pageId = $action->getController()->getRoute();
        $request = Yii::app()->request;

        if ($request->getParam('_lang')) {
            $lang = $request->getParam('_lang');
        } elseif (isset($request->cookies['_lang'])) {
            $lang = $request->cookies['_lang']->value;
        } elseif ($request->getPreferredLanguage()) {
            $lang = 'ru';
        }

        if (empty($lang) || empty($allowed[$lang]) || !in_array($pageId, $allowed[$lang])) {
            $lang = Yii::app()->getLanguage();
        }

        Yii::app()->setLanguage($lang);

        if (empty($request->cookies['_lang']) || $request->cookies['_lang'] !== $lang) {
            $cookie = new CHttpCookie('_lang', $lang);
            $cookie->expire = time() + 86400 * 365;
            Yii::app()->request->cookies['_lang'] = $cookie;
        }

        return true;
    }

    public function getAssetsUrl()
    {
        return Yii::app()->getAssetManager()
            ->publish(
                Yii::getPathOfAlias('application.assets'),
                false,
                -1
            );
    }

    /**
     * Is user authenticated
     */
    public function checkUser()
    {
        $this->user = Yii::app()->user;
        if ($this->user->isGuest) {
            $this->redirect('/user/auth');
        }
        $this->user = $this->user->data();
    }

    /**
     * Is user authenticated and has DEV rights
     */
    public function checkUserDeveloper()
    {
        $this->user = Yii::app()->user;

        if ($this->user->isGuest) {
            $this->redirect('/user/auth');
        }

        if (false == $this->user->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE)) {
            $this->redirect('/');
        }

        $this->user = $this->user->data();
    }

    /**
     * Base user verification
     */
    public function accountPagesBase()
    {
        $user = Yii::app()->user;
        if (null === $user->id) {
            //Yii::app()->user->setFlash('error', 'Авторизируйтесь.');
            $this->redirect('/');
        }

        /**
         * @var YumUser $user
         */
        $user = $user->data();  //YumWebUser -> YumUser

        if (null === Yii::app()->user->data()->getAccount()) {
            $this->redirect('/registration/choose-account-type');
        }

        if ($user->isCorporate()) {
            $this->forward($this->getBaseViewPath().'/corporate');
        }

        if ($user->isPersonal()) {
            $this->forward($this->getBaseViewPath().'/personal');
        }

        // just to be sure - handle strange case
        //Yii::app()->user->setFlash('error', 'Ваш профиль не активирован. Проверьте почтовый ящик - там долно быть письма со ссылкой доя активации аккаунта.');
        $this->redirect('/');
    }
}