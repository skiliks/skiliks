
<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>

<div class="transparent-boder profilewrap">

<?php $this->renderPartial('_menu_corporate', ['active' => ['password' => true]]) ?>

    <div class="form profileform radiusthree">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'account-corporate-password-form'
        )); ?>

        <div class="row">
            <p class="text16"><?php echo Yii::t('site', 'Вы можете изменить пароль'); ?></p>
        </div>

        <div class="row">
            <?php echo $form->labelEx($passwordForm, 'currentPassword'); ?>
            <?php echo $form->passwordField($passwordForm, 'currentPassword'); ?>
            <?php echo $form->error($passwordForm, 'currentPassword'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($passwordForm, 'password'); ?>
            <?php echo $form->passwordField($passwordForm, 'password'); ?>
            <?php echo $form->error($passwordForm, 'password'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($passwordForm, 'verifyPassword'); ?>
            <?php echo $form->passwordField($passwordForm, 'verifyPassword'); ?>
            <?php echo $form->error($passwordForm, 'verifyPassword'); ?>
        </div>
        <?php if($is_done) { ?>
            <div class="done-password-change">Новый пароль был сохранен</div>
        <?php }else{ ?>
        <div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), ['name' => 'save']); ?>
        </div>
        <?php } ?>

        <?php $this->endWidget(); ?>
    </div>
</div>
<script>
    $(document).ready(function(){
        var errors = $(".errorMessage");
        for (var i=0; i < errors.length;i++) {
            var inp = $(errors[i]).prev("input.error");
            $(inp).css({"border":"2px solid #bd2929"});
            $(errors[i]).addClass($(inp).attr("id"));
        }
    });
</script>