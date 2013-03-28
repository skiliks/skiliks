
<h2 class="thetitle"><?php echo Yii::t('site', 'Password recovery') ?></h2>

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
        <?php echo CHtml::submitButton(Yii::t('site', 'Restore')); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>