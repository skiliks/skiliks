
<h1 class="page-header"><?php echo Yii::t('site', 'Profile') ?></h1>

<div class="container-3 block-border border-primary bg-transparnt">

    <div class="border-primary bg-yellow standard-left-box"><?php $this->renderPartial('//new/_menu_corporate', ['active' => ['password' => true]]) ?></div>

    <div class="border-primary bg-light-blue standard-right-box">
        <div class="pad-large profileform profilelabel-wrap profile-min-height">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'account-corporate-password-form'
            )); ?>

            <div class="row">
                <p class="text16"><?php if($is_done) { echo Yii::t('site', 'Ваш новый пароль сохранён'); }else{ echo Yii::t('site', 'Вы можете изменить пароль'); } ?></p>
            </div>

            <div class="row row-inputs">
                <?php echo $form->labelEx($passwordForm, 'currentPassword'); ?>
                <?php echo $form->passwordField($passwordForm, 'currentPassword'); ?><?php echo $form->error($passwordForm, 'currentPassword'); ?>
            </div>

            <div class="row row-inputs">
                <?php echo $form->labelEx($passwordForm, 'password'); ?>
                <?php echo $form->passwordField($passwordForm, 'password'); ?><?php echo $form->error($passwordForm, 'password'); ?>
            </div>

            <div class="row row-inputs">
                <?php echo $form->labelEx($passwordForm, 'verifyPassword'); ?>
                <?php echo $form->passwordField($passwordForm, 'verifyPassword'); ?><?php echo $form->error($passwordForm, 'verifyPassword'); ?>
            </div>
            <?php if($is_done) { ?>
                <div class="done-password-change"></div>
            <?php }else{ ?>
            <div class="row buttons">
                <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), ['name' => 'save', 'class' => 'btn btn-large btn-green']); ?>
            </div>
            <?php } ?>

            <?php $this->endWidget(); ?>
        </div>
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