<div class="sidebar-nav">
    <br/>
    <?php $this->widget('zii.widgets.CMenu',array(
        'activeCssClass' => 'active',
        'activateItems' => true,
        'items'=>array(
            array(
                'label' => 'Home',
                'url'   => ['admin_area/AdminPages/Dashboard'],
                'visible' => true,

            ),
            array(
                'label' => 'Приглашения',
                'url'   => ['admin_area/AdminPages/Invites'],
                'visible' => true,

            ),
            array(
                'label' => 'Симуляции',
                'url'   => ['admin_area/AdminPages/Simulations'],
                'visible' => true,

            ),
            array(
                'label' => 'Рейтинг симуляций',
                'url'   => ['admin_area/AdminPages/SimulationsRating'],
                'visible' => true,

            ),
            array(
                'label' => '--------------------------',
                'url'   => ['#'],
                'visible' => true,

            ),
            array(
                'label' => 'Пользователи',
                'url'   => ['admin_area/AdminPages/UsersList'],
                'visible' => true,

            ),
            array(
                'label' => 'Корпоративные аккаунты',
                'url'   => ['admin_area/AdminPages/CorporateAccountList'],
                'visible' => true,

            ),
            array(
                'label' => 'Заказы',
                'url'   => ['admin_area/AdminPages/Orders'],
                'visible' => true,

            ),
            array(
                'label' => 'Отзывы',
                'url'   => ['admin_area/AdminPages/FeedBacksList'],
                'visible' => true,

            ),
            array(
                'label' => 'Список подписавшихся на рассылку',
                'url'   => ['admin_area/AdminPages/SubscribersList'],
                'visible' => true,

            ),
            array(
                'label' => '--------------------------',
                'url'   => ['#'],
                'visible' => true,

            ),
            array(
                'label' => 'Текущие симуляции',
                'url'   => ['admin_area/AdminPages/LiveSimulations'],
                'visible' => true,

            ),
            array(
                'label' => 'Импорты',
                'url'   => ['admin_area/AdminPages/ImportsList'],
                'visible' => true,

            ),
            array(
                'label' => 'Статистика',
                'url'   => ['admin_area/AdminPages/Statistics'],
                'visible' => true,

            ),
            array(
                'label' => 'Очередь писем',
                'url'   => ['admin_area/AdminPages/EmailQueue'],
                'visible' => true,

            ),
            array(
                'label' => 'Рефералы',
                'url'   => ['admin_area/AdminPages/ReferralsList'],
                'visible' => true,

            ),
            array(
                'label' => 'Регистрации',
                'url'   => ['admin_area/AdminPages/RegistrationList'],
                'visible' => true,

            ),
            array(
                'label' => 'Список бесплатных почтовиков',
                'url'   => ['admin_area/AdminPages/NotCorporateEmails'],
                'visible' => true,

            ),

            array(
                'label' => 'Выполнить Expire',
                'url'   => ['admin_area/AdminPages/ExpireInvitesAndTariffPlans'],
                'visible' => true,

            ),
        ),
        'htmlOptions'=>array('class'=>'nav nav-list')
    )) ?>
</div>


