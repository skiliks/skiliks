<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxController
 *
 * @author dorian
 */
class AjaxController extends CController
{
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR   = 0;
    
    public $is_test = false;

    /**
     * @var YumUser
     */
    public $user;
    public $signInErrors = [];

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
            $lang = substr($request->getPreferredLanguage(), 0, 2);
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

    /**
     * 
     * @param integer $status, 2xx, 3xx, 4xx, 5xx
     * @param string $body
     * @param string $content_type
     */
    protected function _sendResponse($status = 200, $body = '', $content_type = 'application/json')
    {
        if (!$this->is_test) {
            header("HTTP/1.0 200 OK");
            header('Content-type: ' . $content_type . '; charset=UTF-8');
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Access-Control-Allow-Origin: *");
        }
        echo $body;

        Yii::app()->end();
    }

    /** 
     * @method void send JSON Writes JSON to output
     */
    protected function sendJSON($data, $status = 200)
    {
        $this->_sendResponse($status, CJSON::encode($data));
    }
    
    /**
     * @deprecated
     * @return integer || CHttpResponse
     */
    public function getSessionId()
    {
        return Yii::app()->session->sessionID;
    }
    
    /**
     * @deprecated
     * @param integer $sessionId
     * @return integer || HttpResponse
     */
    public function getSimulationId($sessionId = null) 
    {
        $simulation = Simulation::model()->findByPk(Yii::app()->session['simulation']);
        
        if (null === $simulation) { 
            $this->returnErrorMessage(sprintf(
                    "Не могу получить симуляцию по ID %d",
                    Yii::app()->session['simulation']
                )
            );
        }
        
        return $simulation->id;
    }
    
    /**
     *
     * @return Simulation || HttpJsonResponce (Error)
     */
    public function getSimulationEntity()
    {
        $simulation = Simulation::model()->findByPk($this->getSimulationId());
        
        if (null !== $simulation) {
            return $simulation;
        } else {
            $this->returnErrorMessage(sprintf(
                'Simulation with id %s doesn`t exists in db.', 
                $this->getSimulationId()
            ));
        }
    }
    
    /**
     * @deprecated
     * @return Users || HttpJsonResponce (Error)
     */
    public function getCurrentUserId()
    {
        return Yii::app()->user->id;
    }


    /**
     * We handle Yii errors and save them to Yii.log.
     * User see just standard notice
     * 
     * @param string $message
     * @param string $sysLog
     * @return HttpJsonResponce
     */
    public function returnErrorMessage($sysLog, $message = 'Ошибка при обработке запроса.')
    {
        $errorId = date('Y-m-d H:i:s #'.rand(100, 999));
        
        // logging system error
        Yii::log(sprintf('Error No. %s. %s, (%s)', $errorId, $sysLog, $message));
        
        // display user error
        $this->_sendResponse(200, CJSON::encode(array(
            'result'  => self::STATUS_ERROR, 
            'message' => sprintf('Error No. %s. %s', $errorId, $message),
        )));
    }
    
    /**
     * @return mixed array:
     *     array(
     *          'result'        => integer  // 0 - error || 1 - success
     *          'data'          => array,   // array with API responce necessary data
     *          'error_message' => string,  // message that will be displayed for user in error ('result' => 0) case
     *      )
     */
    public function processRequest()
    {
        try {
            $apiMethod = $this->initApiMethodObject();
            $apiMethod->validate();
            $data = $apiMethod->process();
            
            // all right way responce
            $this->sendJSON(array(
                'result' => self::STATUS_ERROR,
                'data'   => $data,
            ));
        } catch (FrontendNotificationException $e) {
            // expected error cases
            $this->sendJSON(array(
                'result'        => self::STATUS_SUCCESS,
                'error_message' => $e->getMessage(),
            ));
        } catch (Exception $e) {
            // unexpected error cases

            // log error {
            Yii::log($e->getMessage());
            Yii::log($e->getTraceAsString());
            // log error }
            
            // responce
            $this->sendJSON(array(
                'result' => self::STATUS_ERROR,
            ));
        }
    }
    
    /**
     * @return mixed object, instance of one from components/ApiMethods classes
     * 
     * @throws FrontendNotificationException
     */
    public function initApiMethodObject()
    {
        $className = sprintf (
            'api%s%s',
            ucfirst(Yii::app()->controller->id),
            ucfirst(Yii::app()->controller->action->id)
        );
        
        if (class_exists($className)) {
            return new $className();
        } else {
            Yii::log(sprintf(
                'Can`t find class %s to init API responce.',
                $className
            ));
            throw new FrontendNotificationException('Invalid API method name.');
        }
    }
    
    public function getAssetsUrl()
    {
        return Yii::app()->getAssetManager()
            ->publish(
                Yii::getPathOfAlias('application.assets'),
                false, 
                -1,
                true
        );
    }

    public function checkUser()
    {
        $this->user = Yii::app()->user;
        if (null === $this->user) {
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
            Yii::app()->user->setFlash('error', 'Авторизируйтесь.');
            $this->redirect('/');
        }

        $user = $user->data();  //YumWebUser -> YumUser

        if (null === Yii::app()->user->data()->getAccount()) {
            $simulation = Simulation::model()->findByAttributes(['user_id' => $user->id]);

            if (null === $simulation) {
                Yii::app()->user->setFlash('error', 'Укажите тип аккаунта.');
                $this->redirect('profile/without-account');
            } else {
                Yii::app()->user->setFlash('error', 'Результаты последней пройденной симуляции.');
                $this->redirect('simulation/results');
            }
        }

        if ($user->isCorporate()) {
            $this->forward($this->getBaseViewPath().'/corporate');
        }

        if ($user->isPersonal()) {
            $this->forward($this->getBaseViewPath().'/personal');
        }

        // just to be sure - handle strange case
        Yii::app()->user->setFlash('error', 'Ваш профиль не активирован. Проверте почтовый ящик - там долно быть письма со ссылкой доя активации аккаунта.');
        $this->redirect('/');
    }
}


