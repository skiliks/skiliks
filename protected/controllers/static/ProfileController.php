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
                $profile->save(false);
                $account->save(false);
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

        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/my-profile-1280.css');
        $this->addSiteCss('pages/my-profile-1024.css');
        $this->addSiteJs('_page-my-profile.js');

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

            $isAccountValid = $account->validate(['position_id']);

            if ($isProfileValid && $isAccountValid) {
                $profile->save(false);
                $account->save(false);
            }
        }

        $positions = [""=>"Выберите должность"];
        foreach (Position::model()->findAll() as $position) {
            $positions[$position->id] = $position->label;
        }

        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/my-profile-1280.css');
        $this->addSiteCss('pages/my-profile-1024.css');
        $this->addSiteJs('_page-my-profile.js');

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

        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/my-profile-1280.css');
        $this->addSiteCss('pages/my-profile-1024.css');
        $this->addSiteJs('_page-my-profile.js');

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

        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/my-profile-1280.css');
        $this->addSiteCss('pages/my-profile-1024.css');
        $this->addSiteJs('_page-my-profile.js');

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

            if ($account->validate(['ownership_type','company_name','industry_id','company_size_id','company_description'])) {
                $account->save(false, ['ownership_type','company_name','industry_id','company_size_id','company_description']);
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

        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/my-profile-1280.css');
        $this->addSiteCss('pages/my-profile-1024.css');
        $this->addSiteJs('_page-my-profile.js');

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

        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/my-profile-1280.css');
        $this->addSiteCss('pages/my-profile-1024.css');
        $this->addSiteJs('_page-my-profile.js');

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
//    public function actionPaymentMethod()
//    {
//        $this->getBaseViewPath = 'PaymentMethod';
//
//        $this->accountPagesBase();
//    }

    /**
     *
     */
//    public function actionCorporatePaymentMethod()
//    {
//        $this->checkUser();
//        if(!$this->user->isCorporate()){
//            $this->redirect('/dashboard');
//        }
//        $this->render('payment_method_corporate', []);
//    }

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
            Yii::app()->user->setFlash('error', 'Вы не можете удалить позицию, с которой уже связаны приглашения');
            $this->redirect($this->createUrl('profile/corporate/vacancies/' ));
        }

        $vacancy->delete();

        $this->redirect($this->createUrl('profile/corporate/vacancies/' ));
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

            $zip = new ZipArchive();

            if (file_exists($pathToZip)) {
                $zip->open($pathToZip, ZIPARCHIVE::OVERWRITE);
            } else {
                $zip->open($pathToZip, ZIPARCHIVE::CREATE);
            }

            if ($path1 == null && $path2 == null) {
                Yii::app()->user->setFlash('error',
                    'У вас нет пройденных симуляций, чтобы сгенерировать на их основе аналитический файл');
                $this->redirect('/dashboard');
            } else {

                // формируем имя для файла-архива {
                $zipFilename = SimulationService::getFileNameForAnalyticalFile($this->user);

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
                    Yii::app()->user->setFlash('error', 'Файл не найден. #err1.');
                    $this->redirect('/dashboard');
                }

                header('Content-Type: application/zip; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $zipFilename . '.zip"');

                echo $zipFile;
            }
        }
    }

    /*
     * Восстанавливает возможность автиризироваться,
     * после того как пользователь несколько на (5 раз) ввёл неправильный пароль
     */
    public function actionRestoreAuthorization() {

        /**
         * @var int
         */
        $user_id = $this->getParam('user_id');

        /**
         * verification key
         * @var string
         */
        $key     = $this->getParam('key');

        /**
         * is it me or not (it was hacker)?
         * @var string
         */
        $type    = $this->getParam('type');

        /*
         * Если не заданы основные параметры, дажене пытаемся восстановить возможность авторизации
         */
        if($user_id !== null && $key !== null && $type !== null) {

            /**
             * А такой пользователь вобще существует?
             * @var YumUser $user
             */
            $user = YumUser::model()->findByPk($user_id);
            if(null !== $user) {

                /*
                 * Попытка взлома зафиксирована?
                 */
                if($user->is_password_bruteforce_detected === YumUser::IS_PASSWORD_BRUTEFORCE_DETECTED) {
                    /*
                     * Ключ правильный?
                     */
                    if($user->authorization_after_bruteforce_key === $key) {
                        if($type === YumUser::PASSWORD_BRUTEFORCE_IT_IS_ME) {
                            /*
                             * "Это я свой пароль забыл..."
                             */
                            UserService::authenticate($user);
                            UserService::logAccountAction($user, $this->request->getUserHostAddress(), 'Пользователь забыл пароль и 5 раз пытался ввести неправильный пароль.');
                            $user->is_password_bruteforce_detected = YumUser::IS_NOT_PASSWORD_BRUTEFORCE;
                            $user->save(false);
                            $this->redirect('/dashboard');

                        } else if($type === YumUser::PASSWORD_BRUTEFORCE_IT_IS_NOT_ME){
                            /**
                             * "Это взлом!"
                             */
                            UserService::authenticate($user);
                            UserService::logAccountAction($user, $this->request->getUserHostAddress(), 'Была попытка подобрать пароль к аккаунту пользователя пользователя. По словам пользователя, это не он.');
                            $user->is_password_bruteforce_detected = YumUser::IS_NOT_PASSWORD_BRUTEFORCE;
                            $user->save(false);
                            Yii::app()->user->setFlash('error', 'Смените, пожалуйста, свой пароль, если он простой');
                            $this->redirect($user->getPasswordChangeUrl());
                        } else {
                            /**
                             * На всякий случай, вероятно, это уже взлом сситемы разблокировки
                             */
                            UserService::logAccountAction($user, $this->request->getUserHostAddress(), 'Человек пытался ввести неверный тип розблокировки!');
                        }
                    } else {
                        /*
                         * Ключ НЕ правильный
                         */
                        UserService::logAccountAction($user, $this->request->getUserHostAddress(), 'Человек пытался ввести неверный ключ розблокировки!');
                    }
                } else {
                    /*
                     * Попытка взлома НЕ зафиксирована
                     */
                    UserService::logAccountAction($user, $this->request->getUserHostAddress(), 'Человек пытался разблокировать аккаунт повторно!');
                }
            }
        }
        $this->redirect('/');
    }

    public function actionSaveFullAssessmentAnalyticFile() {
        $this->checkUser();

        if (!$this->user->isCorporate()) {
            $this->redirect('/dashboard');
        } else {

            // Собираем процентили }

            if(false === UserService::generateFullAssessmentAnalyticFile($this->user)){
                Yii::app()->user->setFlash('error',
                'У вас нет пройденных симуляций, чтобы сгенерировать на их основе аналитический файл');
                $this->redirect('/dashboard');
            }
            // Собираем и группируем симуляции }
            $path = SimulationService::createPathForAnalyticsFile('full_report', 'user_id_'.$this->user->id);
            if(file_exists($path)) {
                $Filename = SimulationService::getFileNameForAnalyticalFile($this->user);
                $Filename.='_full_report';
                header('Content-Type: application/vnd.ms-excel; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $Filename . '.xlsx"');
                $File = file_get_contents($path);
                echo $File;
            }else{
                Yii::app()->user->setFlash('error', 'Файл не найден. #err2.');
                $this->redirect('/dashboard');
            }

        }
    }
}