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
            'visible' => StaticSiteTools::skIsLangSwitcherUrlVisible(Yii::app()->request, Yii::app()->controller)
        ],
        [
            'label'   => Yii::t('site','Help'),
            'url'     => '',
            'visible' => false,
        ],
        [
            'label'   => Yii::t('site', 'My office'),
            'url'     => ['static/dashboard/index'],
            'visible' => !$isGuest && $isActivated
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
