<?php

class AdminPagesController extends AjaxController {

    public function beforeAction($action) {

        $public = ['Login'];
        $user = Yii::app()->user->data();
        if(in_array($action->id, $public)){
            return true;
        }elseif(!$user->isAuth()){
            $this->redirect('/registration');
        }elseif(!$user->isAdmin()){
            $this->redirect('/dashboard');
        }
        return true;
    }

    public function actionDashboard() {

        $this->layout = '//admin_area/layouts/admin_main';
        Yii::app()->user->setFlash('error', "Data saved!");
        $this->render('/admin_area/pages/dashboard', ['user'=>$this->user]);

    }

    public function actionLogin() {

        $form = Yii::app()->request->getParam('YumUserLogin', null);
        $model = new YumUserLogin('login_admin');
        if(null !== $form) {
            $model->setAttributes($form);
            if($model->loginByUsernameAdmin()){
                $model->user->authenticate($form['password']);
                $this->redirect('/admin_area/dashboard');
            }
        }
        $this->layout = '//admin_area/layouts/login';
        $this->render('/admin_area/pages/login', ['model'=>$model]);

    }

    public function actionLogout() {

        if (false === Yii::app()->user->isGuest) {
            Yii::app()->user->logout();
        }
        $this->redirect('/');
    }

    public function actionInvites() {

        $models = Invite::model()->findAll([
            "order" => "updated_at desc"
        ]);
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/invites', ['models'=>$models]);

    }

    public function actionInvitesSave() {

        $this->layout = false;
        $models = Invite::model()->findAll([
            "order" => "updated_at desc"
        ]);
        $csv = "ID-симуляции;";
        $csv .= "Email работодателя;";
        $csv .= "Email соискателя;";
        $csv .= "ID инвайта;";
        $csv .= "Статус инвайта;";
        $csv .= "Время начала симуляции;";
        $csv .= "Время конца симуляции;";
        $csv .= "Тип (название) основного сценария;";
        $csv .= "Оценка\r\n";
        foreach($models as $model) {
        $csv .= (empty($model->simulation->id)?'Не найден':$model->simulation->id).';';
        $csv .= (empty($model->ownerUser->profile->email))?'Не найден':$model->ownerUser->profile->email.';';
        $csv .=(empty($model->receiverUser->profile->email))?'Не найден':$model->receiverUser->profile->email.';';
        $csv .=$model->id.';';
        $csv .=$model->getStatusText().';';
        $csv .=(empty($model->simulation->start)?'---- -- -- --':$model->simulation->start).';';
        $csv .=(empty($model->simulation->end)?'---- -- -- --':$model->simulation->end).';';
        $csv .=(empty($model->scenario->slug)?'Нет данных':$model->scenario->slug).';';
        $csv .=$model->getOverall()."\r\n";
}
        header("Content-type: csv/plain");
        header("Content-Disposition: attachment; filename=invites.csv");
        header("Content-length:".(string)(strlen($csv)));
        echo $csv;
    }

    public function actionSimulationDetail() {
        $sim_id = Yii::app()->request->getParam('sim_id', null);
        @ Yii::app()->request->cookies['display_result_for_simulation_id'] =
            new CHttpCookie('display_result_for_simulation_id', $sim_id);

        $this->redirect('/dashboard');
    }

    public function actionGetBudget() {
        $this->layout = false;
        $sim_id = Yii::app()->request->getParam('sim_id', null);
        $simulation = Simulation::model()->findByPk($sim_id);

        // check document {
        $documentTemplate = $simulation->game_type->getDocumentTemplate([
            'code' => 'D1'
        ]);

        if ($documentTemplate === null) {
            throw new Exception("Файл не найден");
        }

        $document = MyDocument::model()->findByAttributes([
            'template_id' => $documentTemplate->id,
            'sim_id' => $sim_id
        ]);

        $zohoConfigs = Yii::app()->params['zoho'];
        $D1 = $_SERVER['DOCUMENT_ROOT'].'/'.$zohoConfigs['templatesDirPath'].'/'.$document->uuid.'.xls';
        if(file_exists($D1)){
            $xls = file_get_contents($D1);
        }else{
            throw new Exception("Файл не найден");
        }
        $filename = $sim_id.'_'.$documentTemplate->fileName;
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename={$filename}");
        echo $xls;

    }

    public function actionResetInvite() {

        $invite_id = Yii::app()->request->getParam('invite_id', null);
        $invite = Invite::model()->findByPk($invite_id);
        /** @var Invite $invite */
        if (empty($invite)) {
            throw new LogicException('Invite does not exist');
        }

        $result = $invite->resetInvite();
        if(false === $result){
            throw new LogicException("The operation is not successful");
        }

        $this->redirect("/admin_area/invites");
    }

    public function actionOrders() {

        $models = Invoice::model()->findAll([
            "order" => "updated_at desc"
        ]);
        $this->layout = '//admin_area/layouts/admin_main';
        $this->render('/admin_area/pages/orders', ['models'=>$models]);

    }

    public function actionOrderChecked() {

        $order_id = Yii::app()->request->getParam('order_id', null);
        /* @var $model Invoice */
        $model = Invoice::model()->findByPk($order_id);
        if(null === $model){
            throw new Exception("Order - {$order_id} is not found!");
        }
        $model->is_verified = Invoice::CHECKED;
        if(false === $model->save(false)){
            throw new Exception("Not save");
        }
        $this->redirect("/admin_area/orders");
    }

    public function actionOrderUnchecked() {

        $order_id = Yii::app()->request->getParam('order_id', null);
        /* @var $model Invoice */
        $model = Invoice::model()->findByPk($order_id);
        if(null === $model){
            throw new Exception("Order - {$order_id} is not found!");
        }
        $model->is_verified = Invoice::UNCHECKED;
        $model->status = Invoice::STATUS_PENDING;
        if(false === $model->save(false)){
            throw new Exception("Not save");
        }
        $this->redirect("/admin_area/orders");
    }

}