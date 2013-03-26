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
    .small input[type="text"] {
        width: 50px;
    }
</style>

<h1><?php echo Yii::t('site', 'Profile') ?></h1>

<?php $this->renderPartial('_menu_personal', ['active' => ['personal-data' => true]]) ?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'account-personal-personal-form'
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($profile, 'Name'); ?>
        <?php echo $form->textField($profile, 'firstname'); ?>
        <?php echo $form->textField($profile, 'lastname'); ?>
        <?php echo $form->error($profile, 'firstname'); ?>
        <?php echo $form->error($profile, 'lastname'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($profile  , 'email'); ?>
        <span class="value"><?php echo $profile->email; ?></span>
    </div>

    <div class="row">
        <?php echo $form->labelEx($account     , 'professional_status_id'); ?>
        <?php echo $form->dropDownList($account, 'professional_status_id', $statuses); ?>
        <?php echo $form->error($account       , 'professional_status_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($account     , 'industry_id'); ?>
        <?php echo $form->dropDownList($account, 'industry_id', $industries); ?>
        <?php echo $form->error($account       , 'industry_id'); ?>
    </div>

    <div class="row small">
        <?php echo $form->labelEx($account, 'birthday'); ?>
        <?php echo CHtml::textField('birthday[day]',   $account->birthday ? $account->getBirthdayDate()->format('j') : ''); ?>
        <?php echo CHtml::textField('birthday[month]', $account->birthday ? $account->getBirthdayDate()->format('n') : ''); ?>
        <?php echo CHtml::textField('birthday[year]',  $account->birthday ? $account->getBirthdayDate()->format('Y') : ''); ?>
        <?php echo $form->error($account, 'birthday'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($account, 'location'); ?>
        <?php echo $form->textField($account, 'location'); ?>
        <?php echo $form->error($account, 'location'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), ['name' => 'save']); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>
