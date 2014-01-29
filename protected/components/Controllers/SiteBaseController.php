<?php

/**
 * Базовый контроллер для страниц сайта
 */
class SiteBaseController extends CController {

    /**
     * @var $user YumUser
     */
    public $user;
    /**
     * Нужно удалить это
     * @var bool
     */
    public $is_test = false;
    /**
     * ОбЪект запроса
     * @var CHttpRequest
     */
    public $request;

    /**
     * @var CApplication
     */
    public $app;

    /**
     * @var string
     */
    public $assetsUrl;

    /**
     * @var CClientScript
     */
    public $clientScripts;

    /**
     * Определение языка, задание некоторых параметров сайта
     * @param CAction $action
     * @return bool
     */
    protected function beforeAction($action)
    {
        $this->request = &Yii::app()->request;
        if(Yii::app()->params['disableAssets']) {
            /* @var CAssetManager $manager */
            $manager = Yii::app()->getAssetManager();
            $manager->setBasePath('');
            $manager->setBaseUrl('');

            /* @var CClientScript $script_manager */
            $script_manager = Yii::app()->getClientScript();
            $script_manager->setCoreScriptUrl('/framework/web/js/source');
        }
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

        $this->assetsUrl = $this->getAssetsUrl();
        $this->clientScripts = Yii::app()->getClientScript();

        return true;
    }

    /**
     * Возвращает путь к ассертам
     * @return string
     */
    public function getAssetsUrl()
    {
        if(Yii::app()->params['disableAssets']) {
            return '/protected/assets';
        } else {
            return Yii::app()->getAssetManager()
                ->publish(
                    Yii::getPathOfAlias('application.assets'),
                    true, // check assets folder modifiedAs when generate assets folder hash
                    -1
                );
        }

    }

    /**
     * Проверка на то что пользователь авторизирован и получение объекта YumUser
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
     * Проверка что человек авторизирован как разработчик
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
     * Перенаправление на нужную страницу аккаунта
     * Base user verification
     */
    public function accountPagesBase()
    {
        $user = Yii::app()->user;
        if (null === $user->id) {
            //Yii::app()->user->setFlash('error', 'Авторизируйтесь.');
            $this->redirect('/registration');
        }

        /**
         * @var YumUser $user
         */
        $user = $user->data();  //YumWebUser -> YumUser

        if (null === Yii::app()->user->data()->getAccount()) {
            $this->redirect('/registration');
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

    /**
     * Отправка  json
     * @method void send JSON Writes JSON to output
     */
    protected function sendJSON($data, $status = 200)
    {
        $this->_sendResponse($status, CJSON::encode($data));
    }

    /**
     * Отправка ответа от сервера
     * @param integer $status, 2xx, 3xx, 4xx, 5xx
     * @param string $body
     * @param string $content_type
     */
    protected function _sendResponse($status = 200, $body = '', $content_type = 'application/json')
    {
        if (!$this->is_test) {
            header("HTTP/1.0 200 OK");
            if ($status == 500) { header("HTTP/1.0 500 ERROR"); }
            header('Content-type: ' . $content_type . '; charset=UTF-8');
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Access-Control-Allow-Origin: *");
        }

        echo $body;

        Yii::app()->end();
    }

    /**
     * Получение ответа от фронтенда
     * @param $name
     * @param null $defaultValue
     * @return mixed|null
     */
    public function getParam($name, $defaultValue = null) {
        return Yii::app()->request->getParam($name, $defaultValue);
    }

    /**
     * Получение конфига по имени
     * @param $name
     * @return mixed
     */
    public function getConfig($name) {
        return Yii::app()->params[$name];
    }

    /**
     * Добавляет CSS в список файлов подгружаемых со страницей
     * @param string $path, like '_page-dashboard.css'
     */
    public function addSiteCss($path) {
        $this->clientScripts->registerCssFile($this->assetsUrl.'/css/site/'.$path);
    }

    /**
     * @param string $path, like '_page-dashboard.js'
     */
    public function addSiteJs($path) {
        $this->clientScripts->registerScriptFile($this->assetsUrl.'/js/site/'.$path);
    }

    /**
     * Позволяет кратко добавить CSS стиль 'error' или ''
     * в зависимости от того имеет ли поле $fieldName ошибку валидации.
     * Используется в местах где автодобавление класса не работае само-собой
     *
     * @param CActiveForm $form
     * @param mixed $model, object
     * @param string $fieldName
     *
     * @return string
     */
    public function hasErrors($form, $model, $fieldName) {
        return (null == $form->error($model, $fieldName)) ? '' : 'error';
    }
}