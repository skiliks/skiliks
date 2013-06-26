<?php

/** @var YumUser $user */
$user = Yii::app()->user->data();
$isGuest = Yii::app()->user->isGuest;
$isActivated = $user ? $user->isActive() && ($user->isCorporate() ? $user->account_corporate->is_corporate_email_verified : $user->isCorporate() || $user->isPersonal()) : false;

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
            //'visible' => false,
        ],
        [
            'label'       => Yii::t('site', 'My office'),
            'url'         => '',
            'linkOptions' => ['class' => 'link-block'],
        ],
        [
            'label'       => Yii::t('site', 'User name'),
            'url'         => '',
            'linkOptions' => ['class' => 'top-profile top-profile-corp'],
        ],
        [
            'label'       => Yii::t('site', 'Sign in'),
            'url'         => ['/user/auth'],
            'linkOptions' => ['class' => 'font-dark-green sign-in-link'],
            'visible'     => $isGuest && 'ru' == Yii::app()->getLanguage()
        ],
        [
            'label' => Yii::t('site', 'Log out'),
            'url' => ['/static/userAuth/logout'],
            'visible' => !$isGuest,
            'linkOptions' => ['class' => 'font-dark-green log-out-link']
        ],
    ]
));
?>
