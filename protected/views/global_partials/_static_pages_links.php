<?php

/** @var YumUser $user */
$user = Yii::app()->user->data();
$isGuest = Yii::app()->user->isGuest;
$isActivated = $user ? $user->isActive() && ($user->isCorporate() ? $user->account_corporate->is_corporate_email_verified : $user->isCorporate() || $user->isPersonal()) : false;
$visibleName = (!Yii::app()->user->isGuest && $user->isCorporate() || $user->isPersonal())?true:false;
$classForName = '';
$classForName = (!Yii::app()->user->isGuest && $user->isCorporate())?'top-profile-corp':'top-profile-persn';
$profileName = $visibleName?StringTools::getMaxLength(Yii::app()->params['userNameInHeaderMaxLength'], $user->profile->firstname):'';
$this->widget('zii.widgets.CMenu', array(
    'activeCssClass' => 'active',
    'activateItems' => true,
    'items'=>[
        [
            'label'   => Yii::t('site', 'Русский'),
            'url'     => StaticSiteTools::getLangSwitcherUrl(Yii::app()->request, Yii::app()->getLanguage()),
            'visible' => StaticSiteTools::isLangSwitcherUrlVisible(Yii::app()->request, Yii::app()->controller)
        ],
        [
            'label'   => Yii::t('site','Help'),
            'url'     => '',
            'visible' => false,
        ],
        [
            'label'       => Yii::t('site', 'My office'),
            'url'         => '',
            'linkOptions' => ['class' => 'link-block'],
            'visible' => false,
        ],
        [
            'label'       => $profileName,
            'url'         => '',
            'linkOptions' => ['class' => 'top-profile '.$classForName],
            'visible' => $visibleName,
        ],[
            'label'       => Yii::t('site', 'Регистрация'),
            'url'         => ['/registration'],
            'visible'     => $isGuest
        ],
        [
            'label'       => Yii::t('site', 'Sign in'),
            'url'         => ['/user/auth'],
            'linkOptions' => ['class' => 'sign-in-link'],
            'visible'     => $isGuest && 'ru' == Yii::app()->getLanguage()
        ],
        [
            'label' => Yii::t('site', 'Log out'),
            'url' => ['/static/userAuth/logout'],
            'visible' => !$isGuest,
            'linkOptions' => ['class' => 'log-out-link']
        ],
    ]
));
?>
