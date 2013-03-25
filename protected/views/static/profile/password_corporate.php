<style>
    .form {
        margin: 20px 0 0 30px;
        float: left;
        width: 700px;
    }
    .row {
        clear: both;
        margin: 10px 0;
    }
    label {
        float: left;
        width: 200px;
    }
</style>

<h1><?php echo Yii::t('site', 'Profile') ?></h1>

<?php $this->renderPartial('_menu_corporate', ['active' => ['password' => true]]) ?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'account-corporate-password-form'
    )); ?>

    <div class="row">
        <?php echo Yii::t('site', 'Установите новый пароль или <a href="#">восстановите</a> текущий'); ?>
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