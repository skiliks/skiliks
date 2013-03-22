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
        $this->render('personal_data_corporate', []);
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
        $this->render('vacancies_corporate', []);
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
            Yii::app()->uawr->setFlash('error', 'Вы не авторизированы.');
            $this->redirect('/');
        }

        $user = $user->data();  //YumWebUser -> YumUser

        if (null === Yii::app()->user->data()->getAccount()) {
            $this->redirect('registration/choose-account-type');
        }

        if ($user->isCorporate()) {
            // path to controller action (not URL)
            $this->forward('/static/profile/corporate'.$this->getBaseViewPath());
        }

        if ($user->isPersonal()) {
            // path to controller action (not URL)
            $this->forward('/static/profile/personal'.$this->getBaseViewPath());
        }

        // just to be sure - handle strange case
        Yii::app()->uawr->setFlash('error', 'Ваш профиль не активирован. Проверте почтовый ящик - там долно быть письма со ссылкой доя активации аккаунта.');
        $this->redirect('/');
    }
}