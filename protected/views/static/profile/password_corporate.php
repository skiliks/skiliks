
<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>

<div class="transparent-boder profilewrap">

<?php $this->renderPartial('_menu_corporate', ['active' => ['password' => true]]) ?>

    <div class="form profileform radiusthree">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'account-corporate-password-form'
        )); ?>

        <div class="row">
            <p class="text16"><?php echo Yii::t('site', 'Установите новый пароль или <a href="/recovery" class="lbluelink">восстановите</a> текущий'); ?></p>
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

        <div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), ['name' => 'save']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>