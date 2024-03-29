<?php if (
    (preg_match('/(?i)MSIE 6/',$_SERVER['HTTP_USER_AGENT']))
    || (preg_match('/(?i)MSIE 7/',$_SERVER['HTTP_USER_AGENT']))
    || (preg_match('/(?i)MSIE 8/',$_SERVER['HTTP_USER_AGENT']))
    || (preg_match('/(?i)MSIE 9/',$_SERVER['HTTP_USER_AGENT']))
) {
    return '';
}; ?>

<section class="partial">
    <label class="partial-label"><?= __FILE__ ?></label>
        <?php

        /** @var YumUser $user */
        $user = Yii::app()->user->data();
        $isGuest = Yii::app()->user->isGuest;
        $isActivated = $user ? $user->isActive():false;
        $isRegisterByLinkPage = StaticSiteTools::isRegisterByLinkPage(Yii::app()->request->getPathInfo());

        $visibleName = (!Yii::app()->user->isGuest && $user->isCorporate() || $user->isPersonal())?true:false;
        $iconForProfile = '';
        if(!Yii::app()->user->isGuest){
            $user->profile->refresh();
        }
        $iconForProfile = (!Yii::app()->user->isGuest && $user->isCorporate())
            ? 'icon-profile-corporate'
            : 'icon-profile-personal';
        $profileName = $visibleName ? StringTools::getMaxLength(Yii::app()->params['userNameInHeaderMaxLength'], $user->profile->firstname) : '';

        $this->widget('zii.widgets.CMenu', array(
            'activeCssClass' => 'active',
            'activateItems'  => true,
            'itemCssClass'   => 'menu-item',
            'encodeLabel'    => false,
            'items'=>[
                [
                    'label'       => Yii::t('site', 'Русский'),
                    'linkOptions' => ['class' => 'label'],
                    'url'         => StaticSiteTools::getLangSwitcherUrl(Yii::app()->request, Yii::app()->getLanguage()),
                    'visible'     => StaticSiteTools::isLangSwitcherUrlVisible(Yii::app()->request, Yii::app()->controller),
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
                    'linkOptions' => ['class' => ' label '],
                    'visible' => 'ru' == Yii::app()->getLanguage(),
                ],
                [
                    // Ссылка на регистрацию не нужна на странице регистрации по ссылке
                    // Такое же условие естьт и в блоке Вход
                    'label'       => Yii::t('site', 'Регистрация'),
                    'url'         => ['/registration/single-account'],
                    'linkOptions' => ['class' => ' label '],
                    'visible'     => $isGuest && 'ru' == Yii::app()->getLanguage() && false == $isRegisterByLinkPage,
                ],
                [
                    'label'       => Yii::t('site', 'Sign in'),
                    'url'         => ['/user/auth'],
                    'linkOptions' => ['class' => 'action-sign-in locator-button-sign-in label'],
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
                    'linkOptions' => ['class' => 'log-out-link label locator-log-out-link'],
                    'visible'     => !$isGuest,
                ],
            ]
        ));
        ?>
</section>