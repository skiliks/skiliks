
<br/>
<br/>

<h1 class="pull-content-center">
    На указанный вами email
    <?= (isset(Yii::app()->request->cookies['registration_email']))
        ? Yii::app()->request->cookies['registration_email']->value
        : '';
    ?>
    <br/>
    отправлено письмо
</h1>

<br/>
<br/>

<div class="nice-border column-2-3-fixed pull-center pull-content-center
    border-radius-standard background-yellow us-activation-email-notice">
    <div class="pull-content-left us-activation-email-content">
        <h3 class="margin-bottom-half-standard">Активация</h3>
        <span>Пожалуйста, пройдите по ссылке в письме,<br/>
            чтобы активировать аккаунт</span>
    </div>
</div>

<br/>
<br/>

<?php if (!empty($isGuest)): ?>
    <div class="pull-content-center">
        <a href="/registration/single-account"
           class="us-link-register">Начать регистрацию заново</a>
        <?php /*if(null != $profile) : ?>
            <br/>
            <a href="/activation/resend/<?= $profile->id ?>" class="whitelink nodecorlink link-xxlarge">Выслать активационное письмо повторно</a>
        <?php endif;*/ ?>
    </div>
 <?php endif; ?>

<div class="clearfix"></div>