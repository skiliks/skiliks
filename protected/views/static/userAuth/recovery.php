
<h2 class="thetitle"><?php echo Yii::t('site', 'Восстановление пароля') ?></h2>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'password-recovery-form'
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($recoveryForm, 'email'); ?>
        <?php echo $form->textField($recoveryForm, 'email'); ?>
        <?php echo $form->error($recoveryForm, 'email'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton(Yii::t('site', 'Восстановить')); ?>
    </div>

    <?php $this->endWidget(); ?>
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