
<h2 class="title"><?php echo Yii::t('site', 'Sign-up using your preferred option and get the results') ?></h2>

<section class="registration-by-link">
    <div class="text-right"><a href="/user/auth">Вход для зарегистрированных пользователей</a></div>
    <h1>Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию</h1>

    <div class="form form-with-red-errors registration-form ">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'registration-by-link-form'
        )); ?>

        <?= $form->error($this->user, 'username'); ?>
        <?= $form->error($profile, 'email'); ?>

        <div class="row wide">
            <?php echo $form->labelEx($profile, 'Имя'); ?>
            <?php echo $form->textField($profile, 'firstname', ['placeholder' => 'Имя', 'class' => 'shifted']); ?><?php echo $form->error($profile, 'firstname'); ?>
            <?php echo $form->textField($profile, 'lastname', ['placeholder' => 'Фамилия']); ?><?php echo $form->error($profile, 'lastname'); ?>
        </div><div class="row wide">
            <?php echo $form->labelEx($account, 'professional_status_id'); ?>
            <?php echo $form->dropDownList($account, 'professional_status_id', $statuses, ['class' => 'shifted']); ?><?php echo $form->error($account, 'professional_status_id'); ?>
        </div><div class="row wide">
            <?php echo $form->labelEx($user, 'password'); ?>
            <?php echo $form->passwordField($user, 'password', ['class' => 'shifted']); ?><?php echo $form->error($user, 'password'); ?>
        </div><div class="row wide">
            <?php echo $form->labelEx($user, Yii::t("site","Confirmation")); ?>
            <?php echo $form->passwordField($user, 'password_again', ['class' => 'shifted']); ?><?php echo $form->error($user, 'password_again'); ?>
        </div><div class="reg-by-link terms-confirm-left-align">
            <?= $form->error($user, 'agree_with_terms'); ?>
            <?= $form->checkBox($user, 'agree_with_terms', ['value' => 'yes', 'uncheckValue' => null]); ?>
            <?= $form->labelEx($user, 'agree_with_terms', ['label' => 'Я принимаю <a href="#" class="terms">Условия и Лицензионное соглашение</a>']); ?>
        </div><div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t("site","Sign up")); ?>

            <a class="decline-link">Отказаться от приглашения</a>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</section>

<!-- decline-form { -->
<div id="invite-decline-form"></div>
<!-- decline-form } -->

<div class="blackout show"></div>
<style>
    #invite-decline-form{
        z-index:10;
        position: relative;
    }
</style>

<script type="text/javascript">
    // add "blackout"
    $(".blackout").prependTo("body");

    $(function(){
        // decline dialog {
        $.ajax({
            url: '/dashboard/decline-invite/validation',
            type: 'POST',
            success: function(data) {
                $('#invite-decline-form').html(data.html);
                $('#invite-decline-form').hide();

                $('.decline-link').click(function(event){
                    event.preventDefault();
                    $('#invite-decline-form input#DeclineExplanation_invite_id').val('<?php echo $invite->id ?>');

                    $('#invite-decline-form').show();
                });
            }
        })
        // decline dialog }
    })
    $(document).ready(function(){
        var errors = $(".errorMessage");
        for (var i=0; i < errors.length;i++) {
            var inp = $(errors[i]).prev("input.error");
            var select = $(errors[i]).prev(".sbHolder");
            $(errors[i]).addClass($(inp).attr("id"));

            var sbHolder = $(errors[i]).parent().find('.sbHolder');
            console.log(sbHolder);
            sbHolder.addClass("sbHolder_error");
        }
    });
</script>