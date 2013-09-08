<?php
    /* @var $user YumUser */
    $user = Yii::app()->user->data();
?>

<div class="nav-collapse collapse">
    <p class="navbar-text pull-right" style="font-weight: bold;">
        <i class="icon-user icon-white" style="opacity: 0.5;"></i>
        &nbsp;
        <a href="/admin_area/user/<?= $user->id ?>/details" class="navbar-link">
            <?= $user->profile->firstname ?>
            <?= $user->profile->lastname ?>
        </a>
        &nbsp;  &nbsp; | &nbsp; &nbsp;
        <a href="/admin_area/logout" class="navbar-link">Выйти</a>
    </p>
    <?php $this->widget('zii.widgets.CMenu',array(
        'activeCssClass' => 'active',
        'activateItems' => true,
        'items' => array(
            /*array(
                'label'   => 'Home',
                'url'     => ['admin_area/AdminPages/Dashboard'],
                'visible' => true,
            ),*/
        ),
        'htmlOptions'=> ['class'=>'nav'],
    )) ?>
</div>