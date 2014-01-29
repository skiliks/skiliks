<!-- sign-in-box  -->
<div class="hide locator-box-sign-in">
    <h3>Вход</h3>

    <?php $loginWidget = $this->beginWidget('CActiveForm', [
        'id'          => 'login-form',
        'htmlOptions' => ['class' => 'login-form'],
        'action'      => Yii::app()->request->hostInfo.'/user/auth',
        'enableAjaxValidation' => true,
        'clientOptions'        => array(
              'validateOnSubmit' => true,
              'validateOnChange' => false,
              'afterValidate'    => 'js:authenticateValidation',
        )
    ]); ?>

        <?php $loginForm = new YumUserLogin; ?>

        <?= CHtml::hiddenField("returnUrl", '/dashboard') ?>
        <?= CHtml::hiddenField("ajax", 'login-form') ?>

        <div class="row row-13">
            <span class="action-password-recovery pull-right inter-active unstandard-password-recovery">
                <?php echo Yii::t('site', 'Forgot your password?') ?>
            </span>
        </div>

        <div class="row">
            <span class="error-place">
                <?php echo $loginWidget->error($loginForm, 'username'); ?>
            </span>
            <?php echo $loginWidget->textField($loginForm, "username", ['placeholder' => Yii::t('site', 'Enter login')]) ?>
        </div>

        <div class="row">
            <span class="error-place">
                <?php echo $loginWidget->error($loginForm, 'password'); ?>
            </span>
            <?php echo $loginWidget->passwordField($loginForm, "password", ['placeholder' => Yii::t('site', 'Enter password')]) ?>
        </div>

        <?php // for "your email not activated" message { ?>
        <div class="row">
            <?php echo CHtml::error($loginForm, 'form') ?>
        </div>
        <?php // for "your email not activated" message } ?>

        <div class="row-13">
            <input type="checkbox" name="YumUserLogin[rememberMe]" class="reset-margin vertical-align-top"/>
            <label class="vertical-align-top"><?php echo Yii::t('site', 'Remember me') ?></label>
            <a class="pull-right unstandard-registration-link" href="/registration"><?php echo Yii::t('site', 'Registration') ?></a>
        </div>

        <div class="row bottom-margin-standard">
            <?php echo CHtml::submitButton( Yii::t('site', 'Sign in'), [
                'class' => 'button-white inter-active label icon-arrow-blue reset-margin'
            ]); ?>
        </div>

    <?php $this->endWidget(); ?>
</div>

<!-- Восстановление пароля -->

<div class="locator-password-recovery hide">
    <h3>Восстановление пароля</h3>
    <div class="">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id'                   => 'password-recovery-form',
            'action'               => Yii::app()->request->hostInfo.'/recovery',
            'enableAjaxValidation' => true,
            'clientOptions'        => array(
                  'validateOnSubmit' => true, // Required to perform AJAX validation on form submit
                  'afterValidate'    => 'js:passwordRecoverySubmit', // Your JS function to submit form
            )
        )); ?>

        <?php $recoveryForm = new YumPasswordRecoveryForm(); ?>

        <div class="row">
            <span class="error-place">
                <?php echo $form->error($recoveryForm, 'email'); ?>
            </span>
            <?php echo $form->textField($recoveryForm, 'email', ['placeholder'=>Yii::t("site", "Enter email")]); ?>
        </div>

        <div class="row">
            <?php echo CHtml::submitButton(Yii::t('site', 'Восстановить'),[
                'class' => 'button-white inter-active label icon-arrow-blue reset-margin'
            ]); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>



