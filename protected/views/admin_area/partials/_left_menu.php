<div class="sidebar-nav">
    <br/>
    <?php $this->widget('zii.widgets.CMenu',array(
        'activeCssClass' => 'active',
        'activateItems'  => true,
        'encodeLabel'    => false,
        'htmlOptions'    => ['class'=>'nav nav-list'],
        'items' => [
            [
                'label' => 'Home',
                'url'   => ['admin_area/AdminPages/Dashboard'],
                'visible' => true,

            ], [
                'label' => 'Приглашения',
                'url'   => ['admin_area/AdminInvites/Invites'],
                'visible' => true,

            ], [
                'label' => 'Симуляции',
                'url'   => ['admin_area/AdminPages/Simulations'],
                'visible' => true,

            ], [
                'label' => 'Рейтинг симуляций',
                'url'   => ['admin_area/AdminPages/SimulationsRating'],
                'visible' => true,

            ], [
                'label' => '--------------------------',
                'url'   => ['#'],
                'visible' => true,

            ], [
                'label' => 'Пользователи',
                'url'   => ['admin_area/AdminPages/UsersList'],
                'visible' => true,

            ], [
                'label' => '<i class="icon-briefcase"></i>Корпоративные аккаунты',
                'url'   => ['admin_area/AdminPages/CorporateAccountList'],
                'visible' => true,

            ], [
                'label' => 'Заказы',
                'url'   => ['admin_area/AdminInvoices/Orders'],
                'visible' => true,

            ], [
                'label' => 'Отзывы',
                'url'   => ['admin_area/AdminPages/FeedBacksList'],
                'visible' => true,

            ], [
                'label' => 'Список подписавшихся на рассылку',
                'url'   => ['admin_area/AdminPages/SubscribersList'],
                'visible' => true,

            ], [
                'label' => 'Лог авторизации',
                'url'   => ['admin_area/AdminPages/SiteLogAuthorization'],
                'visible' => true,

            ], [
                'label' => 'Список админов',
                'url'   => ['admin_area/AdminPages/AdminsList'],
                'visible' => true,

            ], [
                'label' => 'Заблокированые пользователи',
                'url'   => ['admin_area/AdminPages/UserBlockedAuthorizationList'],
                'visible' => true,

            ], [
                'label' => '--------------------------',
                'url'   => ['#'],
                'visible' => true,
            ], [
                'label' => 'Текущие симуляции',
                'url'   => ['admin_area/AdminPages/LiveSimulations'],
                'visible' => true,
            ], [
                'label' => 'Импорты',
                'url'   => ['admin_area/AdminPages/ImportsList'],
                'visible' => true,
            ], [
                'label' => 'Проверка итоговых оценок',
                'url'   => ['admin_area/AdminServicePages/CheckAssessmentResults'],
                'visible' => true,
            ],  [
                'label' => 'Генерация сводного аналитического файла',
                'url'   => ['admin_area/AdminServicePages/GenerateConsolidatedAnalyticFileResults'],
                'visible' => true,
            ], [
                'label' => 'Статистика',
                'url'   => ['admin_area/AdminPages/Statistics'],
                'visible' => true,
            ], [
                'label' => '<i class="icon-envelope"></i>Очередь писем',
                'url'   => ['admin_area/AdminPages/EmailQueue'],
                'visible' => true,
            ], [
                'label' => 'Регистрации',
                'url'   => ['admin_area/AdminPages/RegistrationList'],
                'visible' => true,
            ], [
                'label' => 'Список бесплатных почтовиков',
                'url'   => ['admin_area/AdminPages/NotCorporateEmails'],
                'visible' => true,
            ], [
                'label' => 'Настройки проекта',
                'url'   => ['admin_area/AdminProjectConfig/ProjectConfigsList'],
                'visible' => true,
            ], [
                'label' => '--------------------------',
                'url'   => ['#'],
                'visible' => true,
            ], [
                'label' => 'Картинки для ПРББ',
                'url'   => ['admin_area/AdminPrbb/ImageArchivesList'],
                'visible' => true,
            ],
        ],
    )) ?>
</div>


