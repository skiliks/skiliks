<h2 class="thetitle text-center">На указанный вами email <?=Yii::app()->session->get("email");?> отправлено письмо</h2>
<div class="form registrationform">
    <div class="transparent-boder">
        <div class="radiusthree yellowbg">
            <div class="registerpads">
                <a class="regicon icon-mail"></a>
                <h3>Активация</h3>
                <ul>
                    <li>Пожалуйста, пройдите по ссылке в письме, </li>
                    <li>чтобы активировать аккаунт</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($isGuest)): ?>
<p class="text-center">
    <a href="/registration/" class="whitelink nodecorlink link-xxlarge">Начать регистрацию заново</a>
    <?php if(null != $profile) : ?>
        &nbsp;&nbsp;
        <a href="/activation/resend/<?= $profile->id ?>" class="whitelink nodecorlink link-xxlarge">Выслать активационное письмо повторно</a>
    <?php endif; ?>
</p>
<?php endif; ?>