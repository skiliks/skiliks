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
                'label' => 'Заказы',
                'url'   => ['admin_area/AdminPages/Orders'],
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
                'label' => 'Статистика',
                'url'   => ['admin_area/AdminPages/Statistics'],
                'visible' => true,

            ),
            /*array('label'=>'About', 'url'=>array('/site/page')),
            array('label'=>'Contact', 'url'=>array('/site/contact')),
            array('label'=>'Login', 'url'=>array('/site/login'))*/
        ),
        'htmlOptions'=>array('class'=>'nav nav-list')
    )) ?>
</div>


