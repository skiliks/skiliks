<?php
class SiteController extends SiteBaseController
{
    /**
     * This is defaut Yii action.
     * It never useded in API or frontend static pages.
     * So, we display error message for user if aout script call this action.
     */
    public function actionIndex()
    {
        $this->forward('static/page/index');
    }

    /**
     *
     */
    public function actionSimulation($mode, $type = Scenario::TYPE_LITE, $invite_id = null)
    {
        /**
         * IE8 валится, выполняя, JS-проверку браузера -- и перенаправления на /old-browser не происходит
         * Поэтому, добавлена эта проверка на уровне PHP
         * @link:http://stackoverflow.com/questions/16474948/detect-ie10-ie10-and-other-browsers-in-php
         */
        if(preg_match('/(?i)msie [5|6|7|8|9]/', $_SERVER['HTTP_USER_AGENT']))
        {
            $this->redirect('/old-browser');
        }


        $start = Yii::app()->request->getParam('start');
        $user = Yii::app()->user->data();
        /* @var $user YumUser  */
        $assetsUrl = $this->getAssetsUrl();
        $check = UserService::getSimulationContentsAndConfigs($user, $assetsUrl, $mode, $type, $invite_id, $start);
        if( $check->return ) {
            $this->layout = false;
            $check->data['httpUserAgent'] = str_replace(['(',')'], '', $_SERVER['HTTP_USER_AGENT']);
            $this->render('site', $check->data);
        } else {
            $this->redirect($check->redirect);
        }
    }

    /**
     * We handle Yii errors and save them to Yii.log.
     * User see just standard notice
     */
    public function actionError()
    {
        $this->returnErrorMessage(Yii::app()->errorHandler->error);
    }

    /**
     *
     */
    public function actionRunSimulationOrChooseAccount()
    {
        $this->render('runSimulationOrChooseAccount');
    }

    /**
     *
     */
    public function actionError404()
    {
        $error = Yii::app()->errorHandler->error;

        if( $error )
        {
            $this->render('error404');
        }
    }

    public function actionWatchVideo() {
        $this->render('watchVideo');
    }

    public function actionIsStarted()
    {
        $invite_id = Yii::app()->request->getParam('invite_id');
        /* @var */
        $invite = Invite::model()->findByPk($invite_id);
        if(InviteService::isSimulationOverrideDetected($invite)){
            InviteService::logAboutInviteStatus($invite, 'Проверка на запущеную симуляцию');
            $result['user_try_start_simulation_twice'] = true;
        }else{
            $result['user_try_start_simulation_twice'] = false;
        }

        $user = Yii::app()->user->data();
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $countInvites = Invite::model()->countByAttributes([
            'owner_id'    => $user->id,
            'receiver_id' => $user->id,
            'status'      => Invite::STATUS_IN_PROGRESS,
            'scenario_id' => $scenario->id,
        ]);

        $result['count_self_to_self_invites_in_progress'] = $countInvites;

        $this->sendJSON($result);
    }

    public function actionBreakSimulationsForSelfToSelfInvites()
    {
        $user = Yii::app()->user->data();
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $invites = Invite::model()->findAllByAttributes([
            'owner_id'    => $user->id,
            'receiver_id' => $user->id,
            'status'      => Invite::STATUS_IN_PROGRESS,
            'scenario_id' => $scenario->id,
        ]);

        foreach ($invites as $invite) {
            if (null !== $invite->simulation) {
                $invite->simulation->status = Simulation::STATUS_INTERRUPTED;
                $invite->simulation->save(false);
                $initValue = $user->getAccount()->getTotalAvailableInvitesLimit();
                $user->getAccount()->invites_limit++;

                UserService::logCorporateInviteMovementAdd(sprintf("Симуляция номер %s прервана ( приглашение номер %s). В аккаунт возвращена одна симуляция.",
                    $invite->simulation->id,$invite->id), $user->getAccount(), $initValue);

            }
            $invite->status = Invite::STATUS_DELETED;
            $invite->save(false);
        }

        $user->getAccount()->save();
    }

    public function actionUserStartSecondSimulation() {

        $invite_id = Yii::app()->request->getParam('invite_id');
        if(null!==$invite_id){
            $invite = Invite::model()->findByPk($invite_id);
            InviteService::logAboutInviteStatus($invite, 'Пользователь запустил вторую симуляцию');
        }
    }

    public function actionUserRejectStartSecondSimulation() {
        $invite_id = Yii::app()->request->getParam('invite_id');
        if(null!==$invite_id){
            $invite = Invite::model()->findByPk($invite_id);
            InviteService::logAboutInviteStatus($invite, 'Пользователь запустил вторую симуляцию');
        }
    }


    public function actionExit(){

        Yii::app()->user->setFlash('error','Текущая симуляция была прервана, так как вы начали новую симуляцию');

        $this->redirect('/dashboard');
    }

    public function actionDemo(){
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $invite = new Invite();
        $invite->scenario = $scenario;
        $invite->scenario_id = $scenario->id;
        $invite->is_display_simulation_results = 1;
        $invite->setExpiredAt();
        $invite->save(false);

        $this->redirect('/simulation/promo/lite/'.$invite->id);
    }
}


