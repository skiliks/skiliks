
<h1 class="page-header"><?php echo Yii::t('site', 'Profile') ?></h1>

<div class="container-3 block-border border-primary bg-transparnt">
    <div class="border-primary bg-yellow standard-left-box"><?php $this->renderPartial('//new/_menu_corporate', ['active' => ['company-info' => true]]) ?></div>

    <div class="border-primary bg-light-blue standard-right-box profile-min-height">
        <div class="pad-large profileform profilelabel-wrap">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'account-corporate-company-info-form'
        )); ?>

        <div class="row blueplaceholder row-inputs">
            <?php echo $form->labelEx($account, 'Название компании'); ?>
            <?php echo $form->textField($account, 'ownership_type', ['placeholder' => 'Форма']); ?><?php echo $form->error($account, 'ownership_type'); ?><?php echo $form->textField($account, 'company_name', ['placeholder' => 'Название']); ?><?php echo $form->error($account, 'company_name'); ?>
        </div>

        <div class="row row-selects">
            <?php echo $form->labelEx($account     , 'industry_id'); ?>
            <?php echo $form->dropDownList($account, 'industry_id', $industries); ?>
            <?php echo $form->error($account       , 'industry_id'); ?>
        </div>

        <div class="row row-selects">
            <?php echo $form->labelEx($account     , 'company_size_id'); ?><?php echo $form->dropDownList($account, 'company_size_id', $sizes, ['prompt' => 'Количество сотрудников']); ?><?php echo $form->error($account       , 'company_size_id'); ?>
        </div>

        <div class="row row-textarea">
            <?php echo $form->labelEx($account, 'company_description'); ?><?php echo $form->textArea($account, 'company_description', ['rows' => 5, 'cols' => 50]); ?><?php echo $form->error($account, 'company_description'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), ['name' => 'save', 'class' => 'btn btn-large btn-green']); ?>
        </div>

        <?php $this->endWidget(); ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        var errors = $(".errorMessage");
        for (var i=0;i<errors.length;i++) {
            var inp = $(errors[i]).prev("input.error");
            $(inp).css({"border":"2px solid #bd2929"});
            $(errors[i]).addClass($(inp).attr("id"));
        }
    });
</script>