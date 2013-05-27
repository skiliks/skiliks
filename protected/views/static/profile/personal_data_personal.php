<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>

<div class="transparent-boder profilewrap">

<?php $this->renderPartial('_menu_personal', ['active' => ['personal-data' => true]]) ?>

    <div class="form profileform radiusthree">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'account-personal-personal-form'
        )); ?>

        <div class="row">
            <?php echo $form->labelEx($profile, 'firstname'); ?>
            <?php echo $form->textField($profile, 'firstname'); ?><?php echo $form->error($profile, 'firstname'); ?>
            <?php echo $form->textField($profile, 'lastname'); ?><?php echo $form->error($profile, 'lastname'); ?>
        </div>

        <div class="row rowup">
            <?php echo $form->labelEx($profile  , 'Email'); ?>
            <span class="value"><?php echo $profile->email; ?></span>
        </div>

        <div class="row">
            <?php echo $form->labelEx($account     , 'professional_status_id'); ?>
            <?php echo $form->dropDownList($account, 'professional_status_id', $statuses); ?><?php echo $form->error($account       , 'professional_status_id'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($account     , 'industry_id'); ?>
            <?php echo $form->dropDownList($account, 'industry_id', $industries); ?>
            <?php echo $form->error($account       , 'industry_id'); ?>
        </div>

        <div class="row small blueinputtext">
            <?php echo $form->labelEx($account, 'birthday'); ?>
            <?php echo $form->textField($account, 'birthday[day]', array('value'=>$account->getBirthdayDate('d'), 'placeholder'=>'ДД')); ?>
            <?php echo $form->textField($account, 'birthday[month]', array('value'=>$account->getBirthdayDate('m'), 'placeholder'=>'ММ')); ?>
            <?php echo $form->textField($account, 'birthday[year]', array('value'=>$account->getBirthdayDate('Y'), 'placeholder'=>'ГГГГ')); ?>
            <?php echo $form->error($account, 'birthday'); ?>
        </div>

        <div class="row blueinputtext">
            <?php echo $form->labelEx($account, 'location'); ?>
            <?php echo $form->textField($account, 'location'); ?>
            <?php echo $form->error($account, 'location'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), ['name' => 'save']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>