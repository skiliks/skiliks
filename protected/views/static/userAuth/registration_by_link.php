<div class="blackout show"></div>
<h2 class="title"><?php echo Yii::t('site', 'Sign-up using your preferred option and get the results') ?></h2>

<section class="registration-by-link">
    <div class="text-right"><a href="/user/auth">Вход для зарегистрированных пользователей</a></div>
    <h1>Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию</h1>

    <div class="form">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'registration-by-link-form'
        )); ?>

        <?= $form->error($this->user, 'username'); ?>
        <?= $form->error($profile, 'email'); ?>

        <div class="row">
            <?php echo $form->labelEx($profile, 'Имя'); ?>
            <?php echo $form->textField($profile, 'firstname', ['placeholder' => 'Имя']); ?><?php echo $form->error($profile, 'firstname'); ?>
            <?php echo $form->textField($profile, 'lastname', ['placeholder' => 'Фамилия']); ?><?php echo $form->error($profile, 'lastname'); ?>
        </div><div class="row wide">
            <?php echo $form->labelEx($account, 'professional_status_id'); ?>
            <?php echo $form->dropDownList($account, 'professional_status_id', $statuses); ?><?php echo $form->error($account, 'professional_status_id'); ?>
        </div><div class="row">
            <?php echo $form->labelEx($user, 'password'); ?>
            <?php echo $form->passwordField($user, 'password'); ?><?php echo $form->error($user, 'password'); ?>
        </div><div class="row">
            <?php echo $form->labelEx($user, Yii::t("site","Confirmation")); ?>
            <?php echo $form->passwordField($user, 'password_again'); ?><?php echo $form->error($user, 'password_again'); ?>
        </div><div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t("site","Sign up")); ?>

            <a class="decline-link">Отказаться от приглашения</a>
        </div><div class="reg-by-link terms-confirm">
            <?= $form->checkBox($user, 'agree_with_terms', ['value' => 'yes', 'uncheckValue' => null]); ?><?= $form->labelEx($user, 'agree_with_terms', ['label' => 'Я принимаю <a href="#" class="terms">Условия и Лицензионное соглашение</a>']); ?>
            <?= $form->error($user, 'agree_with_terms'); ?>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</section>

<!-- decline-form { -->
<div id="invite-decline-form"></div>
<!-- decline-form } -->

<style>
    #invite-decline-form{
        z-index:10;
        position: relative;
    }
</style>

<script type="text/javascript">
    $(".blackout").prependTo("body");
    /*$('.decline-invite').click(function() {
        var href = $(this).attr('href');

        $('.decline-form-box').removeClass('hidden');

        return false;
    });

    $('.decline-form .back').click(function() {
        $('.decline-form-box').addClass('hidden');
    });*/
$(function(){
    // decline dialog {
    $.ajax({
        url: '/dashboard/decline-invite/validation',
        type: 'POST',
        success: function(data) {
            $('#invite-decline-form').html(data.html);
            $('#invite-decline-form').hide();
            //$('#invite-decline-form').dialog({
            //    width: 500,
            //    modal: true
            //});

            //$('#invite-decline-form').parent().addClass('nice-border');
            //$('#invite-decline-form').parent().addClass('backgroud-rich-blue');

            //$('#invite-decline-form').dialog('close');

            $('.decline-link').click(function(event){
                event.preventDefault();
                $('#invite-decline-form input#DeclineExplanation_invite_id').val('<?php echo $invite->id ?>');

                $('#invite-decline-form').show();
                //$('#invite-decline-form').dialog('open');
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

        $(inp).css({"border":"2px solid #bd2929"});
        $(select).css({"border":"2px solid #bd2929"});
        //$(errors[i]).css("bottom",($(inp).height()+5));
        //$(errors[i]).width($(inp).outerWidth());

        $(errors[i]).addClass($(inp).attr("id"));
        $(errors[i]).addClass("sbHolder_err");
    }
});
</script>