<?php
$webUser = Yii::app()->user;
/** @var YumUser $user */
$user = $webUser->data();

$isCorporate = $user->isCorporate();
$isPersonal = $user->isPersonal();
$isGuest = $webUser->isGuest;
$isActivated = $user->isActive() && ($isCorporate ? $user->account_corporate->is_corporate_email_verified : $isCorporate || $isPersonal);

if ($isPersonal) {
    $count = Invite::model()->countByAttributes([],
        ' email = :email AND status = :status AND owner_id != :id ',
    [
        'id' => $user->id,
        'email' => $user->profile->email,
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
        ['label' => Yii::t('site', 'Home'), 'url' => ['/static/pages/index'], 'visible' => $isGuest],
        ['label' => Yii::t('site','About Us'), 'url' => ['/static/pages/team'], 'visible' => $isGuest],
        ['label' => Yii::t('site', 'Product'), 'url' => ['/static/pages/product'], 'visible' => $isGuest],
        ['label' => Yii::t('site', 'Pricing & Plans'), 'url' => ['/static/pages/tariffs'], 'visible' => $isGuest],
        [
            'label'   => Yii::t('site', 'Work dashboard'),
            'url'     => ['/static/dashboard/index'],
            'visible' => $isCorporate && $isActivated && !$isGuest,
            'active'  => strpos(Yii::app()->request->getPathInfo(), 'dashboard') === 0
        ],

        [
            'label'   => Yii::t('site', 'Personal dashboard'),
            'url'     => ['/static/dashboard/index'],
            'visible' => $isPersonal && $isActivated && !$isGuest,
            'active'  => strpos(Yii::app()->request->getPathInfo(), 'dashboard') === 0
        ],
        [
            'label' => Yii::t('site', 'Profile'),
            'url' => ['/static/profile/index'],
            'visible' => $isActivated && !$isGuest,
            'active' => strpos(Yii::app()->request->getPathInfo(), 'profile') === 0
        ],
        ['label' => Yii::t('site', 'Statistics'), 'url' => '', 'visible' => false],
        ['label' => Yii::t('site', 'Notifications'), 'url' => '', 'visible' => false],
        [
            'label'       => Yii::t('site', 'Simulations') . ($count ? "<span class='not-started-simulations'>$count</span>" : ""),
            'url'         => ['/static/simulations/index'],
            'visible'     => $isActivated && !$isGuest,
            'linkOptions' => $count ? ['class' => 'has-notification'] : []
        ],
        [
            'label'   => Yii::t('site', 'Cheats'), 'url' => ['/static/cheats/mainPage'],
            'visible' => Yii::app()->user->data()->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE),

        ],

    ]
));
?>
