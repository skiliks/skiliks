
<h2 class="thetitle"><?php echo Yii::t('site', 'Change password') ?></h2>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'change-password-form'
    )); ?>

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

    <div class="row buttons">
        <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения')); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>