<?/* @var $user YumUser */
    $user = Yii::app()->user->data();
?>
<div class="nav-collapse collapse">
    <p class="navbar-text pull-right">
        <?=$user->profile->firstname?> <?=$user->profile->lastname?> <a href="/admin_area/logout" class="navbar-link">Выйти</a>
    </p>
    <? $this->widget('zii.widgets.CMenu',array(
        'items'=>array(
            array('label'=>'Рабочая панель', 'url'=>array('/admin_area/dashboard')),
            array('label'=>'Инвайты', 'url'=>array('/admin_area/invites')),
            array('label'=>'Contact', 'url'=>array('/site/contact')),
            array('label'=>'Login', 'url'=>array('/site/login'))
        ),
        'htmlOptions'=>array('class'=>'nav')
    )) ?>
</div>