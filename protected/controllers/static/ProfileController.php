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
        $this->render('personal_data_personal', []);
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
    public function actionPersonalPassword()
    {
        $this->render('password_personal', []);
    }

    /**
     *
     */
    public function actionCorporatePassword()
    {
        $this->render('password_corporate', []);
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
        $this->render('company_info_corporate', []);
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
        $vacancy = new Vacancy();

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
                'Выбирите род деятельности'
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
}