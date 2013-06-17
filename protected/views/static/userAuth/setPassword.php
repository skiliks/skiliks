<div class="change_pass">
    <!--<h2 class="thetitle"><?php echo Yii::t('site', 'Change password') ?></h2>-->

    <div class="form">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'change-password-form'
        )); ?>

        <div class="row">
            <!--<?php echo $form->labelEx($passwordForm, 'password'); ?>-->
            <?php echo $form->passwordField($passwordForm, 'password', ['placeholder'=>Yii::t("site","Enter new password")]); ?>
            <?php echo $form->error($passwordForm, 'password'); ?>
        </div>

        <div class="row">
            <!--<?php echo $form->labelEx($passwordForm, 'verifyPassword'); ?>-->
            <?php echo $form->passwordField($passwordForm, 'verifyPassword', ['placeholder'=>Yii::t("site","Confirm password")]); ?>
            <?php echo $form->error($passwordForm, 'verifyPassword'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения')); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>

<script>
    $('.change_pass').dialog({
        closeOnEscape: true,
        dialogClass: 'change_pass-pop-up',
        draggable: false,
        minHeight: 220,
        modal: true,
        resizable: false,
        position: {
            my: "right top",
            at: "right bottom",
            of: $('#top header #static-page-links')
        },
        title: 'Сменить пароль',
        width: 275,
        open: function( event, ui ) { Cufon.refresh(); }
    });
    //$('.flash-pop-up .ui-dialog-titlebar').remove();
    $('.change_pass').dialog('open');

    $(document).ready(function(){
        var errors = $(".errorMessage");
        for (var i=0; i < errors.length;i++) {
            var inp = $(errors[i]).prev("input");
            $(inp).css({"border":"2px solid #bd2929"});
            $(errors[i]).addClass($(inp).attr("id"));
        }
    });
</script>