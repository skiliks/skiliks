<?php

/**
 * Контроллер сервисных страниц:
 *  - статистика
 *  - консольные команды выполняемые из админки
 *
 * Class AdminServicePagesController
 */
class AdminServicePagesController extends SiteBaseController {

    public $user;

    public function beforeAction($action) {

        $public = ['Login'];
        $user = Yii::app()->user->data();
        $this->user = $user;
        if(in_array($action->id, $public)){
            parent::beforeAction($action);
            return true;
        }elseif(!$user->isAuth()){
            $this->redirect('/admin_area/login');
        }elseif(!$user->isAdmin()){
            $this->redirect('/dashboard');
        }
        parent::beforeAction($action);
        return true;
    }

    public function actionCheckAssessmentResults() {

        $currentCheck = SiteLogCheckResults::model()->findByAttributes(['finished_at' => null]);
        $allCheckLogs = SiteLogCheckResults::model()->findAll(['order' => 'id DESC']);

        if (null == $currentCheck && 'check' == Yii::app()->request->getParam('action')) {
            ini_set('max_execution_time', 60*60*0.5); // 30 min

            $simulations = Simulation::model()->findAll(
                " results_popup_cache is not null
                and scenario_id = 2
                and start > '2013-08-01 00:00:00'
                and status = 'complete' "
            );

            // симуляций меньше 1000, можно и циклом пройтись
            $userSimulations = [];
            foreach ($simulations as $simulation) {
                if ('tatiana@skiliks.com' == $simulation->user->profile->email
                    || 'tony@skiliks.com' == $simulation->user->profile->email
                    || 'vladimir@skiliks.com' == $simulation->user->profile->email
                    || 'vladimir1@skiliks.com' == $simulation->user->profile->email
                    || 'tetyana.grybok@skiliks.com' == $simulation->user->profile->email
                    || 'sarnavskyi89@gmail.com' == $simulation->user->profile->email
                    || 'andrey.sarnavskiy@skiliks.com' == $simulation->user->profile->email) {
                    continue;
                }
                $userSimulations[] = $simulation;
            }

            unset($simulations);

            /**
             * @var CheckAssessmentResults $component
             */
            $component = new CheckAssessmentResults();
            $component->checkAndFix($userSimulations, true);
        }

        $this->layout = '/admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/service/check_assessment_values', [
            'user'         => $this->user,
            'currentCheck' => $currentCheck,
            'allCheckLogs' => $allCheckLogs,
        ]);
    }
}