
<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>

<div class="transparent-boder profilewrap">
<?php $this->renderPartial('_menu_personal', ['active' => ['password' => true]]) ?>

    <div class="form profileform radiusthree">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'account-corporate-password-form'
        )); ?>

        <div class="row">
            <?php echo Yii::t('site', 'Установите новый пароль или <a href="/recovery" class="lbluelink">восстановите</a> текущий'); ?>
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