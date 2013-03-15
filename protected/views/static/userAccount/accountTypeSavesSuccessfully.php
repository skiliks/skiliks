<br/>
<br/>
<br/>
<?php echo sprintf(
    Yii::t('site', 'Your account successfully updated to "%s".'),
    Yii::t('site', $user->getAccountType())
); ?>

<?php if ($user->isCorporate() && false == (bool)$user->getAccount()->is_corporate_email_verified) : ?>
    <br/>
    <br/>
    <?php echo Yii::t('site', 'We send corporate-email-address verification email to you.<br/> Please, confirm your corporate email by link in this letter.') ?>
<?php endif; ?>