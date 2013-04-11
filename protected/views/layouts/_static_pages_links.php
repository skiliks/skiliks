<?php
$this->widget('zii.widgets.CMenu', array(
    'activeCssClass' => 'active',
    'activateItems' => true,
    'items'=>[
        ['label' => Yii::t('site', 'Русский'), 'url' => '/' . Yii::t('site','ru')],
        ['label' => Yii::t('site','Help'), 'url' => ''],
        ['label' => Yii::t('site', 'My office'), 'url' => ['static/dashboard/index']],
        [
            'label' => Yii::t('site', 'Sign in'),
            'url' => ['/user/auth'],
            'linkOptions' => ['class' => 'sign-in-link'],
            'visible' => Yii::app()->user->isGuest && 'ru' == Yii::app()->getLanguage()
        ],
        ['label' => Yii::t('site', 'Log out'), 'url' => ['static/userAuth/logout'], 'visible' => !Yii::app()->user->isGuest ],
    ]
));
?>
