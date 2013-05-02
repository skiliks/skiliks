<?php

function skGetLangSwitcherUrl() {
    $url = Yii::app()->request->getUrl();

    if ('ru' == Yii::app()->getLanguage()) {
        $url = str_replace('/en', '', $url);
        $url = str_replace('/ru', '/en', $url);
        if (false === strpos($url, '/en')) {
            $url .= '/en';
        }
    } else {
        $url = str_replace('/ru', '', $url);
        $url = str_replace('/en', '/ru', $url);
        if (false === strpos($url, '/ru')) {
            $url .= '/ru';
        }
    }

    $url = str_replace('//','/', $url);

    return $url;
}

function skIsLangSwitcherUrlVisible() {
    //var_dump();
    return (0 === strpos(Yii::app()->request->getPathInfo(), 'static/team')) ||
        (0 === strpos(Yii::app()->request->getPathInfo(), 'static/product')) ||
        (0 === strpos(Yii::app()->request->getPathInfo(), 'static/tariffs')) ||
        (Yii::app()->controller->getId() == 'static/pages' && Yii::app()->controller->getAction()->getId() == 'index');
}

/* @var $user YumUser */
//$user = YumUser::model()->findByAttributes(['id'=>Yii::app()->user->id]);
$this->widget('zii.widgets.CMenu', array(
    'activeCssClass' => 'active',
    'activateItems' => true,
    'items'=>[
        [
            'label'   => Yii::t('site', 'Русский'),
            'url'     => skGetLangSwitcherUrl(),
            'visible' => skIsLangSwitcherUrlVisible()
        ],
        [
            'label'   => Yii::t('site','Help'),
            'url'     => '',
            'visible' => false,
        ],
        [
            'label'   => Yii::t('site', 'My office'),
            'url'     => ['static/dashboard/index'],
            'visible' => ('ru' == Yii::app()->getLanguage() && null != Yii::app()->user->data() && null != Yii::app()->user->data()->id)
        ],
        [
            'label'       => Yii::t('site', 'Sign in'),
            'url'         => ['/user/auth'],
            'linkOptions' => ['class' => 'sign-in-link'],
            'visible'     => Yii::app()->user->isGuest && 'ru' == Yii::app()->getLanguage()
        ],
        [
            'label' => Yii::t('site', 'Log out'),
            'url' => ['/static/userAuth/logout'],
            'visible' => !Yii::app()->user->isGuest,
            'linkOptions' => ['class' => 'log-out-link']
        ],
    ]
));
?>
