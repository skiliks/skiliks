
<section class="page-title-box column-full pull-content-center ">
    <h1 class="margin-bottom-standard"><?php echo Yii::t('site', 'Profile') ?></h1>
</section>

<section class="pull-content-left nice-border reset-padding company-info-height us-profile-width font-always-14px
    shadow-14 border-radius-standard background-transparent-20 pull-center">

    <!--div class="transparent-boder profilewrap"-->
    <aside class="inline-block background-yellow border-radius-standard vertical-align-top">
        <?php $this->renderPartial('_menu_corporate', ['active' => ['company-info' => true]]) ?>
    </aside>

    <section class="inline-block border-radius-standard background-F3FFFF
         pull-right pull-content-left vertical-align-top profile-right-side">
        <div class="">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'account-corporate-company-info-form'
            )); ?>

            <div class="row blueplaceholder" style="margin-top: 15px">
                <?php echo $form->labelEx($account, 'Название компании'); ?>
                <?php echo $form->textField($account, 'ownership_type', [
                    'placeholder' => 'Форма',
                    'class'       => 'us-company-form'
                ]); ?>
                <?php echo $form->error($account, 'ownership_type'); ?>
                <?php echo $form->textField($account, 'company_name', [
                    'placeholder' => 'Название',
                    'class'       => 'us-company-name'

                ]); ?>
                <?php echo $form->error($account, 'company_name'); ?>
            </div>

            <div class="row rowindustry">
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
                <?php echo $form->labelEx($account, 'company_description', ['class' => 'vertical-align-top']); ?>
                <?php echo $form->textArea($account, 'company_description', ['rows' => 5, 'cols' => 41]); ?>
                <?php echo $form->error($account, 'company_description'); ?>
            </div>

            <div class="row buttons" style="margin-top: 10px;">
                <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), [
                    'name' => 'save',
                    'class' => 'background-dark-blue icon-circle-with-blue-arrow-big
                        button-standard icon-padding-standard',
                ]); ?>
            </div>

            <?php $this->endWidget(); ?>
        </div>
    </section>
</section>

<div class="clearfix"></div>
