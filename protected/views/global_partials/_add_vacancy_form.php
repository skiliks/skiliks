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
            ),
            [
                'ajax' => [
                    'type'     => 'POST',
                    'dataType' =>'json',
                    'url'      => $this->createUrl('profile/getSpecialization'),
                    'success'  =>' function(data) {
                            $("select#Vacancy_professional_specialization_id option").remove();
                            for (var id in data) {
                                $("select#Vacancy_professional_specialization_id").append(
                                    "<option value=\"" + id + "\">" + data[id] + "</option>"
                                );
                            }

                            if (0 == data.length) {
                                $("select#Vacancy_professional_specialization_id").parent().addClass(\'empty-select\');
                            } else {
                                $("select#Vacancy_professional_specialization_id").parent().removeClass(\'empty-select\');
                            }

                            // refresh custom drop-down
                            $("select#Vacancy_professional_specialization_id").selectbox("detach");
                            $("select#Vacancy_professional_specialization_id").selectbox("attach");
                        }',
                ],
            ]
        ); ?>
        <?php echo $form->error($vacancy       , 'professional_occupation_id'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($vacancy     , 'position_level_slug'); ?>
        <?php echo $form->dropDownList($vacancy, 'position_level_slug', $positionLevels);
        echo $form->error($vacancy       , 'position_level_slug'); ?>
    </div>

    <div class="row <?php echo (0 == count($specializations) ? 'empty-select' : '') ?>">
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
