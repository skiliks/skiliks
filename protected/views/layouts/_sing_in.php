<?php if (!Yii::app()->user->id) : ?>
    <div class="sign-in-box message_window" style="display: none;">
        <form class="login-form" action="/user/auth" method="post">
            <input type="hidden" name="returnUrl" value="/dashboard"/>

            <div class="login">
                <a class="link-recovery" href="#"><?php echo Yii::t('site', 'Forgot your password?') ?></a>
                <input type="text" name="YumUserLogin[username]" placeholder="<?php echo Yii::t('site', 'Enter login') ?>" />
            </div>
            <div class="password">
                <input type="password" name="YumUserLogin[password]" placeholder="<?php echo Yii::t('site', 'Enter password') ?>" />
            </div>
            <div class="remember">
                <input type="checkbox" name="rememberMe" value="remember" class="niceCheck" id="ch1" /> <label for="ch1"><?php echo Yii::t('site', 'Remember me') ?></label>
            </div>
            <div class="errors">
            </div>
            <div class="submit">
                <input type="submit" value="<?php echo Yii::t('site', 'Sign in') ?>">
            </div>
            <!--
            <?php if (null === $this->user || null === $this->user->id || 0 != count($this->signInErrors)) : ?>
                <a href="/registration"><?php echo Yii::t('site', 'Registration') ?></a>
            <?php endif; ?>
            -->
        </form>
    </div>

    <div class="popup-recovery" style="display: none;" title="Восстановление пароля">

        <div class="form">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'password-recovery-form',
                'action'=>Yii::app()->request->hostInfo.'/recovery'
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
        // show/hide sign-in box
        $('.sign-in-link').click(function(event){
            event.preventDefault();
            $(".sign-in-box").dialog('open');
        });
        $(".link-recovery").click(function(){
            $(".sign-in-box").dialog("close");
            $(".popup-recovery").dialog('open');
            $(".popup-recovery").dialog({
                closeOnEscape: true,
                dialogClass: 'sing-in-pop-up', //'popup-recovery-view',
                minHeight: 220,
                modal: true,
                resizable: false,
                position: {
                    my: "right top",
                    at: "right bottom",
                    of: $('#top header #static-page-links')
                },
                width: 275
            });
            return false;
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
            width: 275
        });
        $(".sign-in-box").dialog("close");

    });
</script>
<script>
    $(document).ready(function(){
        var errors = $(".errorMessage");
        for (var i=0;i<errors.length;i++) {
            var inp = $(errors[i]).prev("input.error");
            $(inp).css({"border":"2px solid #bd2929"});
            $(errors[i]).addClass($(inp).attr("id"));
        }
    });
</script>