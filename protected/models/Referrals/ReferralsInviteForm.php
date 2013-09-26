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
            array('emails', 'required', 'message' => Yii::t('site', 'Emails are required')),
            array('emails', 'checkEmails'),
        );
    }

    public function checkEmails()
    {
        if($this->emails != "") {
            // replacing spacing in emails "@, @"
            $emails = str_replace(" ", "", $this->emails);


            $user = Yii::app()->user->data();

            $userEmail = $user->getAccount()->corporate_email;
            $userId = $user->id;

            $userDomain = substr($userEmail, strpos($userEmail, "@"));

            $criteria = new CDbCriteria();
            $criteria->compare('referral_id', $userId);

            $allUserReferrers = Referrer::model()->findAll($criteria);

            $addedEmails = [];

            if(strpos($emails, ",") !== 0) {
                $emails = explode(",", $emails);
            }
            else {
                $emails = [$emails];
            }

            foreach($emails as $referEmail) {

                // referrer domain zone
                $referDomain = substr($referEmail, strpos($referEmail, "@"));

                // Проверка на ту же доменную зону, что у юзера

                if($userDomain == $referDomain) {
                    $this->addError('emails', "Е-мейл ".$referEmail . " принадлежит к доменной группе е-мейла ".$userEmail);
                }

                // проверка на доменную зону у рефералов

                foreach($allUserReferrers as $oldReferrer) {
                    $oldReferDomain = substr($oldReferrer->referrer_email, strpos($oldReferrer->referrer_email, "@"));
                    if($oldReferDomain == $referDomain) {
                        $this->addError('emails', "Е-мейл ".$referEmail . " принадлежит в доменной группе одного из уже приглашенных рефералов");
                        break;
                    }
                }

                // проверка на то, что введены несколько рефераллов с одной доменной зоной

                foreach($this->validatedEmailsArray as $email) {
                    $emailDomainZone = substr($email, strpos($email, "@"));
                    if($emailDomainZone == $referDomain) {
                        $this->addError('emails', "Емейлы рефералов ".$email." и ".$referEmail." принадлежат к одной доменной группе.");
                        break;
                    }
                }

                // проверка на корпоративный e-mail

                if(false == UserService::isCorporateEmail($referEmail)) {
                    $this->addError('emails', 'Е-мейл '. $referEmail .' не является корпоративным.');
                }



                $referrer = new Referrer();
                $referrer->referrer_email = $referEmail;
                $referrer->validate();

                $errorArray = $referrer->getErrors();

                if( !empty($errorArray) ) {
                    foreach($errorArray as $errors) {
                        foreach($errors as $error) {
                            $this->addError('emails', $referEmail . ": " . $error);
                        }
                    }
                }
                else $this->validatedEmailsArray[] = $referrer->referrer_email;
            }
        }
    }

}