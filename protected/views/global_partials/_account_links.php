<?php

/** @var YumUser $user */
$user = Yii::app()->user->data();
$isGuest = Yii::app()->user->isGuest;
$isActivated = $user ? $user->isActive():false;

$visibleName = (!Yii::app()->user->isGuest && $user->isCorporate() || $user->isPersonal())?true:false;
$iconForProfile = '';
$iconForProfile = (!Yii::app()->user->isGuest && $user->isCorporate())
    ? 'icon-profile-corporate'
    : 'icon-profile-personal';
$profileName = $visibleName ? StringTools::getMaxLength(Yii::app()->params['userNameInHeaderMaxLength'], $user->profile->firstname) : '';

$this->widget('zii.widgets.CMenu', array(
    'activeCssClass' => 'active',
    'activateItems'  => true,
    'itemCssClass'   => 'menu-item',
    'items'=>[
        [
            'label'   => Yii::t('site', 'Русский'),
            'url'     => StaticSiteTools::getLangSwitcherUrl(Yii::app()->request, Yii::app()->getLanguage()),
            'visible' => StaticSiteTools::isLangSwitcherUrlVisible(Yii::app()->request, Yii::app()->controller),
        ],
        [
            'label'       => Yii::t('site', 'Additional simulations'),
            'url'         => '/invite/referrals',
            'linkOptions' => ['class' => ' icon-gift icon-padding-condensed '],
            'visible'     => !$isGuest && 'ru' == Yii::app()->getLanguage() && $user->isCorporate() && (Yii::app()->controller->id == "static/dashboard" || Yii::app()->controller->id == "static/profile"),
        ],
        [
            'label'       => Yii::t('site', 'My office'),
            'url'         => '',
            'linkOptions' => ['class' => 'link-block'],
            'visible' => false,
        ],
        [
            'label'            => $profileName,
            'linkOptions'      => ['class' => 'profile-icon ' . $iconForProfile],
            'visible'          => $visibleName,
        ],
        [
            'label'   => Yii::t('site','Help'),
            'url'     => '/help/general',
            'visible' => 'ru' == Yii::app()->getLanguage(),
        ],
        [
            'label'       => Yii::t('site', 'Регистрация'),
            'url'         => ['/registration'],
            'visible'     => $isGuest && 'ru' == Yii::app()->getLanguage(),
        ],
        [
            'label'       => Yii::t('site', 'Sign in'),
            'url'         => ['/user/auth'],
            'linkOptions' => ['class' => 'sign-in-link'],
            'visible'     => $isGuest && 'ru' == Yii::app()->getLanguage(),
        ],
        [
            'label'       => '',
            'linkOptions' => ['class' => 'log-out-link-separator'],
            'visible'     => !$isGuest,
        ],
        [
            'label'       => Yii::t('site', 'Log out'),
            'url'         => ['/static/userAuth/logout'],
            'linkOptions' => ['class' => 'log-out-link'],
            'visible'     => !$isGuest,
        ],
    ]
));
?>
