<?php /* if($error != -1) : ?>
<h3 class="midtitle">
    <?php // echo Yii::t('site','Activation did not work'); ?>
</h3>
<?php endif ?>

<div class="transparent-boder errorblock">
    <div class="radiusthree backgroud-light-blue">
        <a class="popupclose"></a>
        <h3 class="midtitle"><?php if($error == -1) echo Yii::t('site','The user is already activated'); ?></h3>
            <h3 class="midtitle"><?php if($error == -2) echo Yii::t('site','Wrong activation Key'); ?></h3>
                <h3 class="midtitle"><?php if($error == -3) echo Yii::t('site','Profile found, but no associated user. Possible database inconsistency. Please contact the System administrator with this error message, thank you'); ?>
                </h3>
        <p><a href="/user/auth/">Зайдите</a> в свой аккаунт, используя данный email в качестве login.</p>
        <p>Если вы забыли свой пароль, воспользуйтесь сервисом <a href="/recovery/">Восстановить пароль</a></p>
    </div>
</div>

*/ ?>

<h3 class="midtitle">
    <?php var_dump($error) ?>
    <?php if($error == -1) echo Yii::t('site','The user is already activated'); ?>
    <?php if($error == -2) echo Yii::t('site','Wrong activation Key'); ?>
    <?php if($error == -3) echo Yii::t('site','Profile found, but no associated user. Possible database inconsistency. Please contact the System administrator with this error message, thank you'); ?>
</h3>

<p><a href="/user/auth/">Зайдите</a> в свой аккаунт, используя данный email в качестве login.</p>
<p>Если вы забыли свой пароль, воспользуйтесь сервисом <a href="/recovery/">Восстановить пароль</a></p>
