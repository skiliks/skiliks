<div class="blackout show"></div>
<h2 class="title"><?php echo Yii::t('site', 'Sign-up using your preferred option and get the results') ?></h2>

<section class="registration-by-link">
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
        </div><div class="row wide">
            <?php echo $form->labelEx($account, 'industry_id'); ?>
            <?php echo $form->dropDownList($account, 'industry_id', $industries); ?><?php echo $form->error($account, 'industry_id'); ?>
        </div><div class="row">
            <?php echo $form->labelEx($user, 'password'); ?>
            <?php echo $form->passwordField($user, 'password'); ?><?php echo $form->error($user, 'password'); ?>
        </div><div class="row">
            <?php echo $form->labelEx($user, Yii::t("site","Confirmation")); ?>
            <?php echo $form->passwordField($user, 'password_again'); ?><?php echo $form->error($user, 'password_again'); ?>
        </div><div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t("site","Sign up")); ?>

            <a class="decline-link">Отказаться от приглашения</a>
        </div>

        <?php $this->endWidget(); ?>
    </div>
    <?php /* ?>
    <div class="decline-form-box hidden">
        <form class="decline-form" action="/dashboard/decline-invite/<?php echo $invite->code; ?>" method="POST">
            <h3>Пожалуйста укажите причину отказа</h3>

            <div class="row">
                <input type="radio" name="reason" value="1" checked="checked" /> Не хочу регистрироваться
            </div>

            <div class="row">
                <input type="radio" name="reason" value="2" /> Не интересует вакансия
            </div>

            <div class="row">
                <input type="radio" name="reason" value="3" /> Не хочу проходить тест
            </div>

            <div class="row">
                <input type="radio" name="reason" value="4" /> Другое
            </div>

            <div class="row">
                <textarea name="reason-desc" placeholder="Причина отказа"></textarea>
            </div>

            <div class="submit">
                <input type="submit" value="Отказаться">
                <input class="back" type="button" value="Вернуться к регистрации">
            </div>
        </form>
    </div>
    <?php */ ?>
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