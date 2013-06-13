<?/* @var $user YumUser */
    $user = Yii::app()->user->data();
?>
<div class="nav-collapse collapse">
    <p class="navbar-text pull-right">
        <?=$user->profile->firstname?> <?=$user->profile->lastname?> <a href="/admin_area/logout" class="navbar-link">Выйти</a>
    </p>
    <!--<ul class="nav">
        <li class="active"><a href="#">Home</a></li>
        <li><a href="#about">About</a></li>
        <li><a href="#contact">Contact</a></li>
    </ul>-->
    <? $this->widget('zii.widgets.CMenu',array(
        'items'=>array(
            array('label'=>'Рабочая панель', 'url'=>array('/admin_area/dashboard')),
            array('label'=>'About', 'url'=>array('/site/page')),
            array('label'=>'Contact', 'url'=>array('/site/contact')),
            array('label'=>'Login', 'url'=>array('/site/login'))
        ),
        'htmlOptions'=>array('class'=>'nav')
    )) ?>
</div>