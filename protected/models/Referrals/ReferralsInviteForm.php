<?php
/**
 * Created by JetBrains PhpStorm.
 * User: macbookpro
 * Date: 12.09.13
 * Time: 16:50
 * To change this template use File | Settings | File Templates.
 */

class ReferralsInviteForm extends CFormModel {

    /**
     * @var string $emails
     */
    public $emails;

    /**
     * @var string $emails
     */
    public $text;

    /**
     * @var array of string $validatedEmailsArray
     */
    public $validatedEmailsArray = [];

    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('emails', 'required', 'message' => 'Введите email(-ы)'),
            array('emails', 'checkEmails'),
        );
    }

    public function checkEmails()
    {
        if($this->emails != "") {
            // replacing spacing in emails "@, @"
            $this->emails = str_replace(" ", "", $this->emails);
            $this->emails = str_replace("\n", "", $this->emails);
            $this->emails = str_replace("\r", "", $this->emails);
            $this->emails = str_replace("\t", "", $this->emails);

            /* @var $user YumUser */
            $user = Yii::app()->user->data();

            $userEmail = strtolower($user->profile->email);

            if(strpos($this->emails, ",") !== 0) {
                $this->emails = strtolower(explode(",", $this->emails));
            } else {
                $this->emails = [strtolower($this->emails)];
            }

            $tempEmails = $this->emails;

            $i = 0;
            foreach($tempEmails as $email) {
                if($email == "") {
                    unset($this->emails[$i]);
                }
                $i++;
            }

            if(20 < count($this->emails)) {
                $this->addError('emails', 'Вы ввели больше 20 email(-ов)');
            }  else {
                foreach($this->emails as $referralEmail) {

                    // проверка на уже зарегистрированного пользователя
                    $existProfile = YumProfile::model()->findByAttributes([
                        'email' => strtolower($referralEmail)
                    ]);

                    if($existProfile !== null) {
                        $this->addError('emails', 'Пользователь с email '. $referralEmail .' уже зарегистрирован у нас.');
                    }

                    // Проверка одинаковый е-мейл с юзером
                    if($userEmail == $referralEmail) {
                        $this->addError('emails', "Email ".$referralEmail . " совпадает с вашим.");
                    }

                    // проверка на корпоративный e-mail
                    if(false == UserService::isCorporateEmail($referralEmail)) {
                        $this->addError('emails', 'Email '. $referralEmail .' не является корпоративным.');
                    }

                    $referral = new UserReferral();
                    $referral->referral_email = strtolower($referralEmail);
                    $referral->validate();

                    $errorArray = $referral->getErrors();

                    if( !empty($errorArray) ) {
                        foreach($errorArray as $errors) {
                            foreach($errors as $error) {
                                $this->addError('emails', $referralEmail . ": " . $error);
                            }
                        }
                    }
                    else {
                        $this->validatedEmailsArray[] = $referral->referral_email;
                    }
                }
            }
        }
    }

}