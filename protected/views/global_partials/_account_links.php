<?php
$webUser = Yii::app()->user;
/** @var YumUser $user */
$user = $webUser->data();

$isCorporate = $user->isCorporate();
$isPersonal = $user->isPersonal();
$isGuest = $webUser->isGuest;
$isDisplayAccountLinks = (isset($isDisplayAccountLinks)) ? $isDisplayAccountLinks : false;
$isActivated = $user->isActive();

if ($isPersonal) {
    $count = Invite::model()->countByAttributes([],
        ' email = :email AND status = :status AND owner_id != :id ',
    [
        'id'     => $user->id,
        'email'  => strtolower($user->profile->email),
        'status' => Invite::STATUS_ACCEPTED,
    ]);
} else {
    $count = 0;
}
$this->widget('zii.widgets.CMenu', array(
    'encodeLabel' => false,
    'activeCssClass' => 'active',
    'activateItems' => true,
    'items'=>[
        ['label' => Yii::t('site', 'Home'), 'url' => ['/static/pages/index'], 'visible' => $isGuest || false === $isDisplayAccountLinks],
        ['label' => Yii::t('site','About Us'), 'url' => ['/static/pages/team'], 'visible' => $isGuest || false === $isDisplayAccountLinks],
        ['label' => Yii::t('site', 'Product'), 'url' => ['/static/pages/product'], 'visible' => $isGuest || false === $isDisplayAccountLinks],
        ['label' => Yii::t('site', 'Pricing & Plans'), 'url' => ['/static/pages/tariffs'], 'visible' => $isGuest || false === $isDisplayAccountLinks],
        ['label' => Yii::t('site', 'Demo'), 'url' => ['#'], 'visible' => $isGuest && empty($disableDemo) && Yii::app()->language === 'ru', 'linkOptions'=>array('class'=>'start-lite-simulation-btn main-menu-demo', 'data-href'=>'/simulation/demo')],
        [
            'label'   => Yii::t('site', 'Work dashboard'),
            'url'     => ['/static/dashboard/index'],
            'visible' => $isCorporate && $isActivated && !$isGuest && $isDisplayAccountLinks,
            'active'  => strpos(Yii::app()->request->getPathInfo(), 'dashboard') === 0
        ],

        [
            'label'   => Yii::t('site', 'Personal dashboard') . ($count ? "<span class='not-started-simulations'>$count</span>" : ""),
            'url'     => ['/static/dashboard/index'],
            'visible' => $isPersonal && $isActivated && !$isGuest && $isDisplayAccountLinks,
            'active'  => strpos(Yii::app()->request->getPathInfo(), 'dashboard') === 0
        ],
        [
            'label' => Yii::t('site', 'Profile'),
            'url' => ['/static/profile/index'],
            'visible' => $isActivated && !$isGuest && $isDisplayAccountLinks,
            'active' => strpos(Yii::app()->request->getPathInfo(), 'profile') === 0
        ],
        ['label' => Yii::t('site', 'Statistics'), 'url' => '', 'visible' => false],
        ['label' => Yii::t('site', 'Notifications'), 'url' => '', 'visible' => false],
    ]
));
?>
