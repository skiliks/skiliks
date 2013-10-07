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
            array('emails', 'required'),
            array('emails', 'checkEmails'),
        );
    }

    public function checkEmails()
    {
        if($this->emails != "") {
            // replacing spacing in emails "@, @"
            $emails = str_replace(" ", "", $this->emails);
            $emails = str_replace("\n", "", $emails);
            $emails = str_replace("\r", "", $emails);
            $emails = str_replace("\t", "", $emails);

            /* @var $user YumUser */
            $user = Yii::app()->user->data();

            $userEmail = $user->profile->email;

            if(strpos($emails, ",") !== 0) {
                $emails = explode(",", $emails);
            } else {
                $emails = [$emails];
            }

            $tempEmails = $emails;

            $i = 0;
            foreach($tempEmails as $email) {
                if($email == "") {
                    unset($emails[$i]);
                }
                $i++;
            }

            if(20 < count($emails)) {
                $this->addError('emails', 'Вы ввели более 20 е-мейлов.');
            }  else {
                foreach($emails as $referralEmail) {

                    // проверка на уже зарегистрированного пользователя
                    $existProfile = YumProfile::model()->findByAttributes([
                        'email' => $referralEmail
                    ]);

                    if($existProfile !== null) {
                        $this->addError('emails', 'Пользователь с емейлом '. $referralEmail .' уже зарегистрирован у нас.');
                    }

                    // Проверка одинаковый е-мейл с юзером
                    if($userEmail == $referralEmail) {
                        $this->addError('emails', "Е-мейл ".$referralEmail . " совпадает с вашим.");
                    }

                    // проверка на корпоративный e-mail
                    if(false == UserService::isCorporateEmail($referralEmail)) {
                        $this->addError('emails', 'Е-мейл '. $referralEmail .' не является корпоративным.');
                    }

                    $referral = new UserReferral();
                    $referral->referral_email = $referralEmail;
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