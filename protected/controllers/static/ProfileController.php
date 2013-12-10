<?php

class ProfileController extends SiteBaseController implements AccountPageControllerInterface
{
    public $getBaseViewPath = 'PersonalData';

    /**
     * @return string
     */
    public function getBaseViewPath()
    {
        return $this->getBaseViewPath;
    }

    /**
     *
     */
    public function actionIndex()
    {
        $this->getBaseViewPath = 'PersonalData';
        $this->accountPagesBase();
    }

    /**
     *
     */
    public function actionPersonalData()
    {
        $this->getBaseViewPath = 'PersonalData';

        $this->accountPagesBase();
    }

    /**
     *
     */
    public function actionPersonalPersonalData()
    {
        $this->checkUser();
        if(!$this->user->isPersonal()){
            $this->redirect('/dashboard');
        }
        $account = $this->user->account_personal;
        $profile = $this->user->profile;

        if (null !== Yii::app()->request->getParam('save')) {
            $UserAccountPersonal = Yii::app()->request->getParam('UserAccountPersonal');
            $YumProfile = Yii::app()->request->getParam('YumProfile');
            $account->attributes = $UserAccountPersonal;
            $profile->firstname = $YumProfile['firstname'];
            $profile->lastname  = $YumProfile['lastname'];
            $account->setBirthdayDate($UserAccountPersonal['birthday']);//['day'],['month'],['year'] 1910 && (int)$birthday['year'] <= 2010
            $isAccountValid = $account->validate(['birthday']);
            $isProfileValid = $profile->validate(['firstname', 'lastname']);

            if ($isProfileValid && $isAccountValid) {
                $profile->save();
                $account->save();
            }
        }

        $statuses = [""=>"Выберите должность"];
        foreach (ProfessionalStatus::model()->findAll() as $status) {
            $statuses[$status->id] = $status->label;
        }

        $industries = [""=>"Выберите отрасль"];
        foreach (Industry::model()->findAll() as $industry) {
            $industries[$industry->id] = $industry->label;
        }


        $this->render('personal_data_personal', [
            'account' => $account,
            'profile' => $profile,
            'statuses' => $statuses,
            'industries' => $industries
        ]);
    }

    /**
     *
     */
    public function actionCorporatePersonalData()
    {
        $this->checkUser();

        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }

        if (false === $this->user->isActive()) {
            $this->redirect('/');
        }

        $profile = $this->user->profile;
        $account = $this->user->account_corporate;

        if (null !== Yii::app()->request->getParam('save')) {
            $YumProfile = Yii::app()->request->getParam('YumProfile');
            $profile->firstname = $YumProfile['firstname'];
            $profile->lastname  = $YumProfile['lastname'];

            $isProfileValid     = $profile->validate(['firstname', 'lastname']);

            $UserAccountCorporate = Yii::app()->request->getParam('UserAccountCorporate');
            $account->position_id = $UserAccountCorporate['position_id'];

            $isAccountValid = $account->validate();

            if ($isProfileValid && $isAccountValid) {
                $profile->save();
                $account->save();
            }
        }

        $positions = [""=>"Выберите должность"];
        foreach (Position::model()->findAll() as $position) {
            $positions[$position->id] = $position->label;
        }

        $this->render('personal_data_corporate', [
            'profile'   => $profile,
            'account'   => $account,
            'positions' => $positions
        ]);
    }

    /**
     *
     */
    public function actionPassword()
    {


        $this->getBaseViewPath = 'Password';

        $this->accountPagesBase();
    }

    /**
     *
     */
    public function actionCorporatePassword()
    {
        $this->checkUser();

        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }

        $passwordForm = new YumUserChangePassword;
        $passwordForm->scenario = 'user_request';
        $YumUserChangePassword = Yii::app()->request->getParam('YumUserChangePassword');
        $is_done = false;
        if (null !== $YumUserChangePassword) {
            $passwordForm->attributes = $YumUserChangePassword;
            $passwordForm->validate();

            if (!YumEncrypt::validate_password($passwordForm->currentPassword, $this->user->password, $this->user->salt)) {
                $passwordForm->addError('currentPassword', Yii::t('site', 'Wrong current password'));
            }

            if (!$passwordForm->hasErrors()) {
                $this->user->setPassword($passwordForm->password, $this->user->salt);
                $is_done = true;
                //$this->redirect();
            }
        }
        $profile = YumProfile::model()->findByAttributes(['user_id'=>$this->user->id]);

        $this->render('password_corporate', [
            'passwordForm' => $passwordForm,
            'is_done' => $is_done,
            'profile'=>$profile
        ]);
    }

    /**
     *
     */
    public function actionPersonalPassword()
    {
        $this->checkUser();

        if(!$this->user->isPersonal()){
            $this->redirect('/dashboard');
        }

        $passwordForm = new YumUserChangePassword;
        $passwordForm->scenario = 'user_request';
        $YumUserChangePassword = Yii::app()->request->getParam('YumUserChangePassword');
        $is_done = false;
        if (null !== $YumUserChangePassword) {
            $passwordForm->attributes = $YumUserChangePassword;
            $passwordForm->validate();

            if (!YumEncrypt::validate_password($passwordForm->currentPassword, $this->user->password, $this->user->salt)) {
                $passwordForm->addError('currentPassword', Yii::t('site', 'Wrong current password'));
            }

            if (!$passwordForm->hasErrors()) {
                $this->user->setPassword($passwordForm->password, $this->user->salt);
                $is_done = true;
            }
        }
        $profile = YumProfile::model()->findByAttributes(['user_id'=>$this->user->id]);
        $this->render('password_personal', [
            'passwordForm' => $passwordForm,
            'is_done' => $is_done,
            'profile'=>$profile
        ]);
    }

    /**
     *
     */
    public function actionCompanyInfo()
    {
        $this->getBaseViewPath = 'CompanyInfo';

        $this->accountPagesBase();
    }

    /**
     *
     */
    public function actionPersonalCompanyInfo()
    {
        $this->redirect('');
    }

    /**
     *
     */
    public function actionCorporateCompanyInfo()
    {
        $this->checkUser();

        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }

        $account = $this->user->account_corporate;

        if (null !== Yii::app()->request->getParam('save')) {
            $UserAccountCorporate = Yii::app()->request->getParam('UserAccountCorporate');
            $account->ownership_type       = $UserAccountCorporate['ownership_type'];
            $account->company_name         = $UserAccountCorporate['company_name'];
            $account->industry_id          = $UserAccountCorporate['industry_id'];
            $account->company_size_id      = $UserAccountCorporate['company_size_id'];
            $account->company_description  = $UserAccountCorporate['company_description'];

            if ($account->validate()) {
                $account->save();
            }
        }

        $industries = [];
        foreach (Industry::model()->findAll() as $industry) {
            $industries[$industry->id] = $industry->label;
        }

        $sizes = [];
        foreach (CompanySize::model()->findAll() as $size) {
            $sizes[$size->id] = $size->label;
        }

        $this->render('company_info_corporate', [
            'account' => $account,
            'industries' => $industries,
            'sizes' => $sizes
        ]);
    }

    /**
     *
     */
    public function actionVacancies()
    {
        $this->getBaseViewPath = 'Vacancies';

        $this->accountPagesBase();
    }

    /**
     *
     */
    public function actionPersonalVacancies()
    {
        $this->redirect('');
    }

    /**
     *
     */
    public function actionCorporateVacancies()
    {
        $this->checkUser();

        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }
        $vacancy = new Vacancy();

        if (null !== Yii::app()->request->getParam('id')) {
            $vacancy = Vacancy::model()->findByPk(Yii::app()->request->getParam('id'));
            if (null === $vacancy) {
                $vacancy = new Vacancy();
            }
        }
        $specializations = StaticSiteTools::formatValuesArrayLite(
                'ProfessionalSpecialization',
                'id',
                'label',
                "",
                'Выберите специализацию'
            );
        $positionLevels = StaticSiteTools::formatValuesArrayLite(
            'PositionLevel',
            'slug',
            'label',
            '',
            'Выберите уровень позиции'
        );

        $this->render('vacancies_corporate', [
            'vacancy'         => $vacancy,
            'specializations' => $specializations,
            'positionLevels'  => $positionLevels,
        ]);
    }
    /**
     *
     */
    public function actionVacancyAdd()
    {
        $this->checkUser();

        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }
        $errors = [];

        $vacancy = new Vacancy();
        $vacancy->attributes = Yii::app()->request->getParam('Vacancy');

        $id = Yii::app()->request->getParam('id');

        if (null !== $id) {
            $vacancy = Vacancy::model()->findByPk($id);

            if (null === $vacancy) {
                $vacancy = new Vacancy();
            }
        }

        // handle add vacancy {
        if (null !== Yii::app()->request->getParam('add')) {
            $vacancy->attributes = Yii::app()->request->getParam('Vacancy');
            $vacancy->user_id = Yii::app()->user->data()->id;
        }

        if ($vacancy->validate()) {
            $vacancy->save();
            $cookie = new CHttpCookie('recently_added_vacancy_id', $vacancy->id);
            $cookie->expire = time() + 60*60*3;
            Yii::app()->request->cookies['recently_added_vacancy_id'] = $cookie;
            $errors = true;
        } else {
            foreach ($vacancy->getErrors() as $key => $error) {
                $errors['Vacancy_'.$key] = $error;
            }
        }

        $this->sendJSON($errors);
    }

    /**
     *
     */
    public function actionTariff()
    {
        $this->getBaseViewPath = 'Tariff';

        $this->accountPagesBase();
    }

    /**
     *
     */
    public function actionCorporateTariff()
    {
        $this->checkUser();

        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }

        $this->render('tariff_corporate', ['user'=>$this->user]);
    }

    public function actionCorporateReferrals() {
        $this->checkUser();

        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }

        $dataProvider = UserReferral::model()->searchUserReferrals($this->user->id);

        $totalReferrals = UserReferral::model()->countUserReferrals($this->user->id);
        $this->render('referrals_corporate', ["totalReferrals"=>$totalReferrals, 'dataProvider' => $dataProvider]);
    }

    /**
     *
     */
    public function actionPaymentMethod()
    {
        $this->getBaseViewPath = 'PaymentMethod';
        
        $this->accountPagesBase();
    }

    /**
     *
     */
    public function actionCorporatePaymentMethod()
    {
        $this->checkUser();
        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }
        $this->render('payment_method_corporate', []);
    }

    /* ---------------- */

    /**
     * Base user verification
     */
    public function accountPagesBase()
    {
        $user = Yii::app()->user;
        if ($user->isGuest) {
            //@popup
            //Yii::app()->user->setFlash('error', 'Вы не авторизированы.');
            $this->redirect('/');
        }

        $this->user = $user->data();  //YumWebUser -> YumUser

        if ($this->user->isCorporate()) {
            // path to controller action (not URL)
            $this->forward('/static/profile/corporate'.$this->getBaseViewPath());
        }

        if ($this->user->isPersonal()) {
            // path to controller action (not URL)
            $this->forward('/static/profile/personal'.$this->getBaseViewPath());
        }

        // just to be sure - handle strange case
        //Yii::app()->uawr->setFlash('error', 'Ваш профиль не активирован. Проверьте почтовый ящик - там долно быть письма со ссылкой доя активации аккаунта.');
        $this->redirect('/');
    }

    /* --- */

    public function actionGetSpecialization()
    {
        $vacancy = Yii::app()->request->getParam('Vacancy');

        $this->sendJSON(StaticSiteTools::formatValuesArrayLite(
            'ProfessionalSpecialization',
            'id',
            'label',
            " professional_occupation_id = {$vacancy['professional_occupation_id']} ")
        );
    }

    public function actionRemoveVacancy($id)
    {
        $this->checkUser();
        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }
        $vacancy = Vacancy::model()->findByPk($id);

        if ($vacancy->user_id != Yii::app()->user->data()->id) {
            Yii::app()->user->setFlash('error', 'У вас нет прав для удаления этой позиции');
            $this->redirect($this->createUrl('profile/corporate/vacancies/' ));
        }

        // @todo: is we will keep storing deleted invites - we must exclude such invites from query
        $counter = Invite::model()->countByAttributes([
            'vacancy_id' => $vacancy->id,
        ]);
        if (0 < $counter) {
            Yii::app()->user->setFlash('error', 'Вы не можете удалить позицию с которой уже связаны приглашения.');
            $this->redirect($this->createUrl('profile/corporate/vacancies/' ));
        }

        $vacancy->delete();

        $this->redirect($this->createUrl('profile/corporate/vacancies/' ));
    }

    // ----- NEW:

    /**
     * temporary action
     */
    public function actionCorporateTariffNew() {
        $this->checkUser();
        $this->layout = 'site_standard';

        $this->render('//new/tariff_corporate', []);
    }

    /**
     * temporary action
     */
    public function actionCorporateCompanyInfoNew()
    {
        $this->checkUser();

        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }

        $account = $this->user->account_corporate;

        if (null !== Yii::app()->request->getParam('save')) {
            $UserAccountCorporate = Yii::app()->request->getParam('UserAccountCorporate');
            $account->ownership_type       = $UserAccountCorporate['ownership_type'];
            $account->company_name         = $UserAccountCorporate['company_name'];
            $account->industry_id          = $UserAccountCorporate['industry_id'];
            $account->company_size_id      = $UserAccountCorporate['company_size_id'];
            $account->company_description  = $UserAccountCorporate['company_description'];

            if ($account->validate()) {
                $account->save();
            }
        }

        $industries = [];
        foreach (Industry::model()->findAll() as $industry) {
            $industries[$industry->id] = $industry->label;
        }

        $sizes = [];
        foreach (CompanySize::model()->findAll() as $size) {
            $sizes[$size->id] = $size->label;
        }

        $this->layout = 'site_standard';

        $this->render('//new/company_info_corporate', [
            'account' => $account,
            'industries' => $industries,
            'sizes' => $sizes
        ]);
    }

    /**
     * temporary action
     */
    public function actionCorporatePersonalDataNew()
    {
        $this->checkUser();

        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }

        if (false === $this->user->isActive()) {
            $this->redirect('/');
        }

        $profile = $this->user->profile;
        $account = $this->user->account_corporate;

        if (null !== Yii::app()->request->getParam('save')) {
            $YumProfile = Yii::app()->request->getParam('YumProfile');
            $profile->firstname = $YumProfile['firstname'];
            $profile->lastname  = $YumProfile['lastname'];

            $isProfileValid     = $profile->validate(['firstname', 'lastname']);

            $UserAccountCorporate = Yii::app()->request->getParam('UserAccountCorporate');
            $account->position_id = $UserAccountCorporate['position_id'];

            $isAccountValid = $account->validate();

            if ($isProfileValid && $isAccountValid) {
                $profile->save();
                $account->save();
            }
        }

        $positions = [];
        foreach (Position::model()->findAll() as $position) {
            $positions[$position->id] = $position->label;
        }

        $this->layout = 'site_standard';

        $this->render('//new/personal_data_corporate', [
            'profile'   => $profile,
            'account'   => $account,
            'positions' => $positions
        ]);
    }

    /**
     *
     */
    public function actionCorporatePasswordNew()
    {
        $this->checkUser();

        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }

        $passwordForm = new YumUserChangePassword;
        $passwordForm->scenario = 'user_request';
        $YumUserChangePassword = Yii::app()->request->getParam('YumUserChangePassword');
        $is_done = false;
        if (null !== $YumUserChangePassword) {
            $passwordForm->attributes = $YumUserChangePassword;
            $passwordForm->validate();

            if (!YumEncrypt::validate_password($passwordForm->currentPassword, $this->user->password, $this->user->salt)) {
                $passwordForm->addError('currentPassword', Yii::t('site', 'Wrong current password'));
            }

            if (!$passwordForm->hasErrors()) {
                $this->user->setPassword($passwordForm->password, $this->user->salt);
                $is_done = true;
                //$this->redirect();
            }
        }
        $profile = YumProfile::model()->findByAttributes(['user_id'=>$this->user->id]);

        $this->layout = 'site_standard';

        $this->render('//new/password_corporate', [
            'passwordForm' => $passwordForm,
            'is_done' => $is_done,
            'profile'=>$profile
        ]);
    }

    /**
     *
     */
    public function actionCorporateVacanciesNew()
    {
        $this->checkUser();

        if(!$this->user->isCorporate()){
            $this->redirect('/dashboard');
        }
        $vacancy = new Vacancy();

        if (null !== Yii::app()->request->getParam('id')) {
            $vacancy = Vacancy::model()->findByPk(Yii::app()->request->getParam('id'));
            if (null === $vacancy) {
                $vacancy = new Vacancy();
            }
        }
        $specializations = StaticSiteTools::formatValuesArrayLite(
            'ProfessionalSpecialization',
            'id',
            'label',
            "",
            'Выберите специализацию'
        );
        $positionLevels = StaticSiteTools::formatValuesArrayLite(
            'PositionLevel',
            'slug',
            'label',
            '',
            'Выберите уровень позиции'
        );

        $this->layout = 'site_standard';

        $this->render('//new/vacancies_corporate', [
            'vacancy'         => $vacancy,
            'specializations' => $specializations,
            'positionLevels'  => $positionLevels,
        ]);
    }

    /**
     * Возвращает архив с Аналитическими файлами.
     * В случае если файл пуст - он не должен попадать в архив.
     *
     * Если у пользователя нет пройденных симуляций (его или по его приглашению)
     * - экшн генерирует флеш сообщение об этом и перенаправляет пользователя в Кабинет.
     */
    public function actionSaveAssessmentAnalyticFile2()
    {
        $this->checkUser();

        if (!$this->user->isCorporate()) {
            $this->redirect('/dashboard');
        } else {
            // Аналитический файл сводной оценки по версии v1 - должен быть уже готов
            // (мы его создадим при релизе консольной коммандой)
            $path1 = SimulationService::createPathForAnalyticsFile($this->user->id, 'v1');

            if (false === file_exists($path1)) {
                $path1 = null;
            }

            // Аналитический файл со сводной оценко по версии v2 надо всегда генерировать
            $path2 = SimulationService::saveLogsAsExcelReport2ForCorporateUser(
                $this->user->account_corporate,
                'v2'
            );

            $pathToZip = __DIR__ . '/../../system_data/analytic_files_2/analitic_file_' . $this->user->id . '.zip';

            $zip = new ZipArchive;

            if (file_exists($pathToZip)) {
                $zip->open($pathToZip, ZIPARCHIVE::OVERWRITE);
            } else {
                $zip->open($pathToZip, ZIPARCHIVE::CREATE);
            }

            if ($path1 == null && $path2 == null) {
                Yii::app()->user->setFlash('error',
                    'У вас нет пройденных симуляций, чтобы сгенерировать на их основе анатический файл');
                $this->redirect('/dashboard');
            } else {

                // формируем имя для файла-архива {
                $latinCompanyOwnership = StringTools::CyToEnWithUppercase($this->user->getAccount()->ownership_type);
                $latinCompanyName = StringTools::CyToEnWithUppercase($this->user->getAccount()->company_name);

                $latinCompanyOwnership = preg_replace("/[^a-zA-Z0-9]/", "", $latinCompanyOwnership);
                $latinCompanyName = preg_replace("/[^a-zA-Z0-9]/", "", $latinCompanyName);

                $zipFilename = 'analitics_' . date('dmy');
                // формируем имя для файла-архива }

                // добавляем имя компании к имени файла спереди, но только если имя компании не пустое
                if ('' != $latinCompanyName) {
                    $zipFilename = $latinCompanyName . '_' . $zipFilename;
                }
                if ('' != $latinCompanyOwnership) {
                    $zipFilename = $latinCompanyOwnership . '_' . $zipFilename;
                }

                if (null !== $path1) {
                    // задаём псевдоним, чтоб не палить структуру папок нашего сервера
                    $zip->addFile($path1, '/' . $zipFilename . '_ver_2_1.xlsx');
                }

                if (null !== $path2) {
                    // задаём псевдоним, чтоб не палить структуру папок нашего сервера
                    $zip->addFile($path2, '/' . $zipFilename . '_ver_2_2.xlsx');
                }

                if (null !== $path1 || null !== $path2) {
                    $zip->close();
                }

                if (file_exists($pathToZip)) {
                    $zipFile = file_get_contents($pathToZip);
                } else {
                    Yii::app()->user->setFlash('error', 'Файл не найден');
                    $this->redirect('/dashboard');
                }

                header('Content-Type: application/zip; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $zipFilename . '.zip"');

                echo $zipFile;
            }
        }
    }

    public function actionRestoreAuthorization() {
        $user_id = $this->getParam('user_id');
        $key = $this->getParam('key');
        $type = $this->getParam('type');
        if($user_id !== null && $key !== null && $type !== null) {
            /* @var YumUser $user */
            $user = YumUser::model()->findByPk($user_id);
            if(null !== $user) {
                if($user->is_password_bruteforce_detected === YumUser::IS_PASSWORD_BRUTEFORCE_DETECTED) {
                    if($user->authorization_after_bruteforce_key === $key) {
                        if($type === YumUser::PASSWORD_BRUTEFORCE_IT_IS_ME) {
                            UserService::authenticate($user);
                            UserService::logAccountAction($user, $this->request->getUserHostAddress(), 'Пользователь забыл пароль и 5 раз пытался ввести неправильный пароль.');
                            $user->is_password_bruteforce_detected = YumUser::IS_NOT_PASSWORD_BRUTEFORCE;
                            $user->save(false);
                            $this->redirect('/dashboard');
                        } else if($type === YumUser::PASSWORD_BRUTEFORCE_IT_IS_NOT_ME){
                            UserService::authenticate($user);
                            UserService::logAccountAction($user, $this->request->getUserHostAddress(), 'Была попытка подобрать пароль к аккаунту пользователя пользователя. По словам пользователя, это не он.');
                            $user->is_password_bruteforce_detected = YumUser::IS_NOT_PASSWORD_BRUTEFORCE;
                            $user->save(false);
                            Yii::app()->user->setFlash('error', 'Смените, пожалуйста, свой пароль, если он простой.');
                            $this->redirect($user->getPasswordChangeUrl());
                        }else{
                            UserService::logAccountAction($user, $this->request->getUserHostAddress(), 'Человек пытался ввести неверный тип розблокировки!');
                        }
                    } else {
                        UserService::logAccountAction($user, $this->request->getUserHostAddress(), 'Человек пытался ввести неверный ключ розблокировки!');
                    }
                } else {
                    UserService::logAccountAction($user, $this->request->getUserHostAddress(), 'Человек пытался розблокировать аккаунт повторно!');
                }
            }
        }
        $this->redirect('/');
    }
}