<?php $submitButtonTitle = (isset($submitButtonTitle)) ? $submitButtonTitle : 'Добавить'; ?>
<?php $dataUrl = (isset($dataUrl)) ? $dataUrl : '/profile/corporate/vacancies'; ?>

<?php if (isset($h1)): ?>
    <h1><?php echo $h1?></h1>
<?php endif ?>

<?php $form = $this->beginWidget('CActiveForm', [
    'id'          => 'add-vacancy-form',
    'htmlOptions' => ['data-url' => $dataUrl],
    'action'      => '/vacancy/add',
    'enableAjaxValidation' => true,
    'clientOptions' => array(
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'afterValidate' => 'js:addVacancyValidation',
    )
]); ?>

<?php if (null !== $vacancy->id): ?>
    <?= CHtml::hiddenField('add', true) ?>
    <?= CHtml::hiddenField('id', $vacancy->id) ?>
<?php endif ?>

    <div class="row shortSelector">
        <?php echo $form->labelEx($vacancy     , 'professional_occupation_id'); ?>
        <?php echo $form->dropDownList(
            $vacancy,
            'professional_occupation_id',
            StaticSiteTools::formatValuesArrayLite(
                'ProfessionalOccupation',
                'id',
                'label',
                '',
                'Выберите отрасль'
            )
        ); ?>
        <?php echo $form->error($vacancy       , 'professional_occupation_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($vacancy     , 'position_level_slug'); ?>
        <?php echo $form->dropDownList($vacancy, 'position_level_slug', $positionLevels);
        echo $form->error($vacancy       , 'position_level_slug'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($vacancy     , 'professional_specialization_id'); ?>
        <?php echo $form->dropDownList($vacancy, 'professional_specialization_id', $specializations);
        echo $form->error($vacancy       , 'professional_specialization_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($vacancy  , 'label'); ?>
        <?php echo $form->textField($vacancy, 'label');
        echo $form->error($vacancy    , 'label'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($vacancy  , 'link'); ?>
        <?php echo $form->textField($vacancy, 'link');
        echo $form->error($vacancy    , 'link'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton($submitButtonTitle , ['name' => 'add']); ?>
    </div>

    <!-- * Поля обязательные для заполнения-->

<?php $this->endWidget(); ?>
