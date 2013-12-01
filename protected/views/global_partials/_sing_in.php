<?php if (!Yii::app()->user->id) : ?>
    <div class="sign-in-box message_window" style="display: none;">
        <?php $loginWidget = $this->beginWidget('CActiveForm', [
            'id' => 'login-form',
            'htmlOptions' => ['class' => 'login-form'],
            'action' => Yii::app()->request->hostInfo.'/user/auth',
            'enableAjaxValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true, // Required to perform AJAX validation on form submit
                'validateOnChange' => false,
                'afterValidate'    => 'js:authenticateValidation', // Your JS function to submit form
            )
        ]); ?>
            <?php $loginForm = new YumUserLogin; ?>
            <?= CHtml::hiddenField("returnUrl", '/dashboard') ?>
            <?= CHtml::hiddenField("ajax", 'login-form') ?>
            <div class="row login">
                <a class="link-recovery" href="#"><?php echo Yii::t('site', 'Forgot your password?') ?></a>
                <?php echo $loginWidget->error($loginForm, 'username'); ?>
                <?php echo $loginWidget->textField($loginForm, "username", ['placeholder' => Yii::t('site', 'Enter login')]) ?>
            </div>
            <div class="row password">
                <?php echo $loginWidget->error($loginForm, 'password'); ?>
                <?php echo $loginWidget->passwordField($loginForm, "password", ['placeholder' => Yii::t('site', 'Enter password')]) ?>
            </div>

            <?php // for "your email not activated" message { ?>
            <div id="yum-login-global-errors">
                <?php echo CHtml::error($loginForm, 'form') ?>
                <script>
                    <?php // to prevent update text by Cufon - it`s brake link "send activation email again" ?>
                    $('#yum-login-global-errors .errorMessage').addClass('globalErrorMessage');
                    $('#yum-login-global-errors .errorMessage').removeClass('errorMessage');
                </script>
            </div>
            <?php // for "your email not activated" message } ?>

            <div class="remember">
                <input type="checkbox" name="rememberMe" value="remember" class="niceCheck" id="ch1" />
                <label for="ch1"><?php echo Yii::t('site', 'Remember me') ?></label>
                <a class="registration-popup" href="/registration"><?php echo Yii::t('site', 'Registration') ?></a>
            </div>
            <div class="errors">
            </div>
            <div class="submit">
                <?php echo CHtml::submitButton(Yii::t('site', 'Sign in')); ?>
            </div>
        <?php $this->endWidget(); ?>

    </div>

    <div class="popup-recovery" style="display: none;" title="Восстановление пароля">

        <div class="form">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'password-recovery-form',
                'action'=>Yii::app()->request->hostInfo.'/recovery',
                'enableAjaxValidation' => true,
                'clientOptions'=>array(
                    'validateOnSubmit' => true, // Required to perform AJAX validation on form submit
                    'afterValidate'    => 'js:passwordRecoverySubmit', // Your JS function to submit form
                )
            )); ?>
            <?php $recoveryForm = new YumPasswordRecoveryForm; ?>
            <div class="row">
                <?php echo $form->textField($recoveryForm, 'email', ['placeholder'=>Yii::t("site","Enter email")]); ?>
                <?php echo $form->error($recoveryForm, 'email'); ?>
            </div>

            <div class="row buttons">
                <?php echo CHtml::submitButton(Yii::t('site', 'Восстановить')); ?>
            </div>

            <?php $this->endWidget(); ?>
        </div>

    </div>

    <script type="text/javascript">
        $(function () {
            var h=$('.container').height();
            $('.sign-in-box').css('height',h+'px')
        });
    </script>

<?php endif; ?>

<script type="text/javascript">
    $(function () {
        // @link http://www.bulgaria-web-developers.com/projects/javascript/selectbox/
        $("select").selectbox();

        // @link: http://jqueryui.com/dialog/
        $(".sign-in-box").dialog({
            closeOnEscape: true,
            dialogClass: 'sing-in-pop-up',
            minHeight: 220,
            modal: true,
            position: {
                my: "right top",
                at: "right bottom",
                of: $('#top header #static-page-links')
            },
            resizable: false,
            title: '<?php echo Yii::t('site', 'Sign in') ?>',
            width: 275,
            open: function( event, ui ) { /* Cufon.refresh(); */ }
        });
        $(".sign-in-box").dialog("close");

    });
</script>

<style>
    #password-recovery-form .error input#YumPasswordRecoveryForm_email {border:2px solid #BD2929 !important;}
</style>
