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
            'encodeLabel'    => false,
            'activeCssClass' => 'active',
            'activateItems'  => true,
            'itemCssClass'   => 'menu-item label',
            'htmlOptions' => [
                'class' => 'inline-block'
            ],
            'items'=>[
                [
                    'label'   => Yii::t('site', 'Home'),
                    'url'     => ['/static/pages/index'],
                    'visible' => $isGuest || false === $isDisplayAccountLinks,
                ],

                // About us без подменю - для англоязычных страниц
                [
                    'label'   => Yii::t('site','About Us'),
                    'url'     => ['/static/pages/team'],
                    'visible' => ($isGuest || false === $isDisplayAccountLinks) && 'en' == Yii::app()->getLanguage(),
                ],

                // About us с подменю - для русскоязычных страниц
                [
                    'label'   => Yii::t('site','About Us'),
                    'linkOptions'      => ['class' => 'label icon-sub-menu icon-sub-menu-about-us locator-submenu-switcher-about-us
                        action-show-about-us-submenu background-dark-blue-transparent-40-hovered'],
                    'visible' => ($isGuest || false === $isDisplayAccountLinks)  && 'ru' == Yii::app()->getLanguage(),
                    'submenuOptions' => ['class' => 'sub-menu-1 sub-menu-about-us locator-about-us-submenu'],
                    'items'=> [
                            [
                                'label' => Yii::t('site','About Us'),
                                'url'   => ['/static/pages/team'],
                            ],
                            [
                                'label' => Yii::t('site', 'Articles'),
                                'url'   => ['/static/articles'],
                            ],
                            [
                                'label' => Yii::t('site',  'Partners'),
                                'url'   => ['/static/partners'],
                            ],
                        ]
                 ],

                // Продукты без подменю - для англоязычных страниц
                [
                    'label'   => Yii::t('site', 'Product'),
                    'url'     => ['/static/product'],
                    'visible' => ($isGuest || false === $isDisplayAccountLinks) && 'en' == Yii::app()->getLanguage(),
                ],

                // Продукты с подменю - для русскоязычных страниц
                [
                    'label'   => Yii::t('site', 'Product'),
                    'linkOptions'      => ['class' => 'label icon-sub-menu locator-submenu-switcher
                        action-show-product-submenu background-dark-blue-transparent-40-hovered'],
                    'visible' => ($isGuest || false === $isDisplayAccountLinks) && 'ru' == Yii::app()->getLanguage(),
                    'submenuOptions' => ['class' => 'sub-menu-1 locator-product-submenu'],
                    'items'=> [
                        [
                            'label' => 'Бизнес-игра Skiliks',
                            'url'   => ['/static/product'],
                        ],
                        [
                            'label' => 'Диагностика управленческого потенциала',
                            'url'   => ['/static/product-diagnostic'],
                        ],
                    ]
                ],

                [
                    'label'   => Yii::t('site', 'Pricing & Plans'),
                    'url'     => ['/static/pages/tariffs'],
                    'visible' => $isGuest || false === $isDisplayAccountLinks
                ],

                [
                    'label'       => Yii::t('site', 'Demo'),
                    'url'         => ['#'],
                    'visible'     => $isGuest && empty($disableDemo) && Yii::app()->language === 'ru',
                    'linkOptions' => [
                        'class'     => 'action-open-lite-simulation-popup icon-circle-with-blue-arrow icon-padding-condensed',
                        'data-href' =>'/simulation/demo'
                    ]
                ],

                [
                    'label'   => Yii::t('site', 'Work dashboard'),
                    'url'     => ['/static/dashboard/index'],
                    'visible' => $isCorporate && $isActivated && !$isGuest && $isDisplayAccountLinks,
                    'active'  => strpos(Yii::app()->request->getPathInfo(), 'dashboard') === 0
                ],

                [
                    'label'   => Yii::t('site', 'Personal dashboard') . ($count ?
                            "</a><span class='not-started-simulations'>$count</span><a style='display: none;'>"
                            : ""
                        ),
                    'url'     => ['/static/dashboard/index'],
                    'visible' => $isPersonal && $isActivated && !$isGuest && $isDisplayAccountLinks,
                    'active'  => strpos(Yii::app()->request->getPathInfo(), 'dashboard') === 0
                ],

                [
                    'label'   => Yii::t('site', 'Profile'),
                    'url'     => ['/static/profile/index'],
                    'visible' => $isActivated && !$isGuest && $isDisplayAccountLinks,
                    'active'  => strpos(Yii::app()->request->getPathInfo(), 'profile') === 0
                ],

                [
                    'label'   => Yii::t('site', 'Statistics'),
                    'url'     => '',
                    'visible' => false
                ],

                [
                    'label'   => Yii::t('site', 'Notifications'),
                    'url'     => '',
                    'visible' => false
                ],
            ]
        ));
        ?>
</section>