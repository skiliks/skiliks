<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 3/21/13
 * Time: 11:05 PM
 * To change this template use File | Settings | File Templates.
 */

class ProfileController extends AjaxController implements AccountPageControllerInterface
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

        $account = $this->user->account_personal;
        $profile = $this->user->profile;

        if (null !== Yii::app()->request->getParam('save')) {
            $UserAccountPersonal = Yii::app()->request->getParam('UserAccountPersonal');
            $YumProfile = Yii::app()->request->getParam('YumProfile');
            $birthday = Yii::app()->request->getParam('birthday');

            if (!empty($birthday['day']) || !empty($birthday['month']) || !empty($birthday['year'])) {
                if (checkdate((int)$birthday['month'], (int)$birthday['day'], (int)$birthday['year'])) {
                    $account->birthday = $birthday['year'] . '-' . $birthday['month'] . '-' . $birthday['day'];
                } else {
                    $account->addError('birthday', Yii::t('site', 'Incorrect birthday value'));
                }
            } else {
                $account->birthday = null;
            }

            $account->attributes = $UserAccountPersonal;
            $profile->firstname = $YumProfile['firstname'];
            $profile->lastname  = $YumProfile['lastname'];

            $isAccountValid = $account->validate(null, false);
            $isProfileValid = $profile->validate(['firstname', 'lastname']);

            if ($isProfileValid && $isAccountValid) {
                $profile->save();
                $account->save();
            }
        }

        $statuses = [];
        foreach (ProfessionalStatus::model()->findAll() as $status) {
            $statuses[$status->id] = $status->label;
        }

        $industries = [];
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

        $passwordForm = new YumUserChangePassword;
        $passwordForm->scenario = 'user_request';
        $YumUserChangePassword = Yii::app()->request->getParam('YumUserChangePassword');

        if (null !== $YumUserChangePassword) {
            $passwordForm->attributes = $YumUserChangePassword;
            $passwordForm->validate();

            if (!YumEncrypt::validate_password($passwordForm->currentPassword, $this->user->password, $this->user->salt)) {
                $passwordForm->addError('currentPassword', Yii::t('site', 'Your current password is not correct'));
            }

            if (!$passwordForm->hasErrors()) {
                if ($this->user->setPassword($passwordForm->password, $this->user->salt)) {
                    Yii::app()->user->setFlash('info', 'The new password has been saved');
                } else {
                    Yii::app()->user->setFlash('error', 'There was an error saving the password');
                }

                $this->redirect(Yum::module()->returnUrl);
            }
        }

        $this->render('password_corporate', [
            'passwordForm' => $passwordForm
        ]);
    }

    /**
     *
     */
    public function actionPersonalPassword()
    {
        $this->checkUser();

        $passwordForm = new YumUserChangePassword;
        $passwordForm->scenario = 'user_request';
        $YumUserChangePassword = Yii::app()->request->getParam('YumUserChangePassword');

        if (null !== $YumUserChangePassword) {
            $passwordForm->attributes = $YumUserChangePassword;
            $passwordForm->validate();

            if (!YumEncrypt::validate_password($passwordForm->currentPassword, $this->user->password, $this->user->salt)) {
                $passwordForm->addError('currentPassword', Yii::t('site', 'Your current password is not correct'));
            }

            if (!$passwordForm->hasErrors()) {
                if ($this->user->setPassword($passwordForm->password, $this->user->salt)) {
                    Yii::app()->user->setFlash('info', 'The new password has been saved');
                } else {
                    Yii::app()->user->setFlash('error', 'There was an error saving the password');
                }

                $this->redirect(Yum::module()->returnUrl);
            }
        }

        $this->render('password_personal', [
            'passwordForm' => $passwordForm
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
        Yii::app()->language = 'ru';

        $this->checkUser();

        $account = $this->user->account_corporate;

        if (null !== Yii::app()->request->getParam('save')) {
            $UserAccountCorporate = Yii::app()->request->getParam('UserAccountCorporate');
            $account->ownership_type       = $UserAccountCorporate['ownership_type'];
            $account->company_name         = $UserAccountCorporate['company_name'];
            $account->industry_id          = $UserAccountCorporate['industry_id'];
            $account->company_size_id      = $UserAccountCorporate['company_size_id'];
            $account->company_description  = $UserAccountCorporate['company_description'];

            if ($account->validate()) {
                Yii::app()->user->setFlash('success', 'Данные сохранены.');
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
        Yii::app()->language = 'ru';

        $vacancy = new Vacancy();

        if (null !== Yii::app()->request->getParam('id')) {
            $vacancy = Vacancy::model()->findByPk(Yii::app()->request->getParam('id'));

            if (null === $vacancy) {
                $vacancy = new Vacancy();
                Yii::app()->user->setFlash('success', 'Выбранной вами вакансии не существует.');
            }
        }

        // handle add vacancy {
        if (null !== Yii::app()->request->getParam('add')) {

            $vacancy->attributes = Yii::app()->request->getParam('Vacancy');
            $vacancy->user_id
                = Yii::app()->user->data()->id;

            if ($vacancy->validate()) {
                $vacancy->save();

                Yii::app()->user->setFlash('success', 'Вакансия успешно добавлена');

                $this->redirect('/profile/corporate/vacancies/');
            }
        }
        // handle send invitation }

        $specializations = [];
        if (null != $vacancy->professional_specialization_id) {
            $specializations = StaticSiteTools::formatValuesArrayLite(
                'ProfessionalSpecialization',
                'id',
                'label',
                " professional_occupation_id = {$vacancy->professional_occupation_id} ",
                false
            );
        }

        $this->render('vacancies_corporate', [
            'vacancy'         => $vacancy,
            'specializations' => $specializations,
        ]);
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
    public function actionPersonalTariff()
    {
        $this->redirect('');
    }

    /**
     *
     */
    public function actionCorporateTariff()
    {
        $this->render('tariff_corporate', []);
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
    public function actionPersonalPaymentMethod()
    {
        $this->redirect('');
    }

    /**
     *
     */
    public function actionCorporatePaymentMethod()
    {
        $this->render('payment_method_corporate', []);
    }

    /* ---------------- */

    /**
     * Base user verification
     */
    public function accountPagesBase()
    {
        // this page currently will be just RU
        Yii::app()->language = 'ru';

        $user = Yii::app()->user;
        if (null === $user->id) {
            Yii::app()->user->setFlash('error', 'Вы не авторизированы.');
            $this->redirect('/');
        }

        $this->user = $user->data();  //YumWebUser -> YumUser

        if (null === $this->user->getAccount()) {
            $this->redirect('registration/choose-account-type');
        }

        if ($this->user->isCorporate()) {
            // path to controller action (not URL)
            $this->forward('/static/profile/corporate'.$this->getBaseViewPath());
        }

        if ($this->user->isPersonal()) {
            // path to controller action (not URL)
            $this->forward('/static/profile/personal'.$this->getBaseViewPath());
        }

        // just to be sure - handle strange case
        Yii::app()->uawr->setFlash('error', 'Ваш профиль не активирован. Проверте почтовый ящик - там долно быть письма со ссылкой доя активации аккаунта.');
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
        $vacancy = Vacancy::model()->findByPk($id);

        if ($vacancy->user_id != Yii::app()->user->data()->id) {
            Yii::app()->user->setFlash('error', 'У вас нет прав для удаления этой вакансии');
            $this->redirect($this->createUrl('profile/corporate/vacancies/' ));
        }

//        $counter = Invite::model()->countByAttributes(['vacancy_id' => $vacancy->id]);
//        if (0 < $counter) {
//            Yii::app()->user->setFlash('error', 'Вы не можете удалить вакансию с которой уже связвны приглашения.');
//            $this->redirect($this->createUrl('profile/corporate/vacancies/' ));
//        }

        $vacancy->delete();

        Yii::app()->user->setFlash('error', sprintf('Вакансия %s удалена.', $vacancy->label));

        $this->redirect($this->createUrl('profile/corporate/vacancies/' ));
    }
}