
<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>

<div class="transparent-boder profilewrap">
<?php $this->renderPartial('_menu_corporate', ['active' => ['company-info' => true]]) ?>
    <div class="form profileform radiusthree">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'account-corporate-company-info-form'
        )); ?>

        <div class="row">
            <?php echo $form->labelEx($account, 'Name'); ?>
            <?php echo $form->textField($account, 'ownership_type', ['placeholder' => 'Форма']); ?>
            <?php echo $form->textField($account, 'company_name', ['placeholder' => 'Название']); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($account     , 'industry_id'); ?>
            <?php echo $form->dropDownList($account, 'industry_id', $industries); ?>
            <?php echo $form->error($account       , 'industry_id'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($account     , 'company_size_id'); ?>
            <?php echo $form->dropDownList($account, 'company_size_id', $sizes, ['prompt' => 'Количество сотрудников']); ?>
            <?php echo $form->error($account       , 'company_size_id'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($account, 'company_description'); ?>
            <?php echo $form->textArea($account, 'company_description', ['rows' => 5, 'cols' => 50]); ?>
            <?php echo $form->error($account, 'company_description'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), ['name' => 'save']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>