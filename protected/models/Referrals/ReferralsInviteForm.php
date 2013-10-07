<?php
/**
 * Created by JetBrains PhpStorm.
 * User: macbookpro
 * Date: 12.09.13
 * Time: 16:50
 * To change this template use File | Settings | File Templates.
 */

class ReferralsInviteForm extends CFormModel {

    public $emails;
    public $text;
    public $user = null;
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
            $emails = str_replace(" ", "", $this->emails);
            $emails = str_replace("\n", "", $this->emails);
            $emails = str_replace("\r", "", $this->emails);

            /* @var $user YumUser */
            $user = Yii::app()->user->data();

            $userEmail = $user->profile->email;
            $userId = $user->id;

            $userDomain = substr($userEmail, strpos($userEmail, "@"));

            $criteria = new CDbCriteria();
            $criteria->compare('referrer_id', $userId);

            $allUserReferrals = UserReferral::model()->findAll($criteria);

            $addedEmails = [];

            if(strpos($emails, ",") !== 0) {
                $emails = explode(",", $emails);
            }
            else {
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

            if(count($emails) > 20) {
                $this->addError('emails', 'Вы ввели больше 20 email(-ов)');
            }
            else {

                foreach($emails as $referralEmail) {

                    // проверка на уже зарегистрированного пользователя

                    $existProfile = YumProfile::model()->findByAttributes([
                        'email' => $referralEmail
                    ]);

                    if($existProfile !== null) {
                        $this->addError('emails', 'Пользователь с емейлом '. $referralEmail .' уже зарегистрирован у нас.');
                    }

    //                // referrer domain zone
    //                $referralDomain = substr($referralEmail, strpos($referralEmail, "@"));

    //                // Проверка на ту же доменную зону, что у юзера
    //
    //                if($userDomain == $referralDomain) {
    //                    $this->addError('emails', "Е-мейл ".$referralEmail . " принадлежит к доменной группе е-мейла ".$userEmail);
    //                }

                    // Проверка одинаковый е-мейл с юзером

                    if($userEmail == $referralEmail) {
                        $this->addError('emails', "Е-мейл ".$referralEmail . " совпадает с вашим.");
                    }

    //                // проверка на доменную зону у рефералов
    //
    //                foreach($allUserReferrals as $oldReferral) {
    //                    $oldReferralDomain = substr($oldReferral->referral_email, strpos($oldReferral->referral_email, "@"));
    //                    if($oldReferralDomain == $referralDomain) {
    //                        $this->addError('emails', "Е-мейл ".$referralEmail .
    //                            " принадлежит в доменной группе одного из уже приглашенных рефералов");
    //                        break;
    //                    }
    //                }

    //                // проверка на то, что введены несколько рефераллов с одной доменной зоной
    //
    //                foreach($this->validatedEmailsArray as $email) {
    //                    $emailDomainZone = substr($email, strpos($email, "@"));
    //                    if($emailDomainZone == $referralDomain) {
    //                        $this->addError('emails', "Емейлы рефералов ".$email." и ".$referralEmail." принадлежат к одной доменной группе.");
    //                        break;
    //                    }
    //                }

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
                    else $this->validatedEmailsArray[] = $referral->referral_email;
                }
            }
        }
    }

}