<section class="partial">
    <label class="partial-label"><?= __FILE__ ?></label>

        <?php $submitButtonTitle = (isset($submitButtonTitle)) ? $submitButtonTitle : 'Добавить'; ?>
        <?php $dataUrl = (isset($dataUrl)) ? $dataUrl : '/profile/corporate/vacancies'; ?>

        <?php if (isset($h1)): ?>
            <h3 class="pull-content-center"><?php echo $h1?></h3>
        <?php endif ?>

        <?php $form = $this->beginWidget('CActiveForm', [
            'id'          => 'add-vacancy-form',
            'htmlOptions' => ['data-url' => $dataUrl],
            'action'      => '/vacancy/add',
            'enableAjaxValidation' => true,
            'clientOptions' => array(
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'afterValidate'    => 'js:addVacancyValidation',
            )
        ]); ?>

        <?php if (null !== $vacancy->id): ?>
            <?= CHtml::hiddenField('add', true) ?>
            <?= CHtml::hiddenField('id', $vacancy->id) ?>
        <?php endif ?>

            <div class="row shortSelector row-selects">
                <span class="error-place">
                    <?= $form->error($vacancy       , 'professional_occupation_id'); ?>
                </span>
                <?= $form->labelEx($vacancy     , 'professional_occupation_id'); ?>
                <?= $form->dropDownList(
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
            </div>

            <div class="row row-selects">
                <span class="error-place">
                    <?= $form->error($vacancy       , 'position_level_slug'); ?>
                </span>
                <?= $form->labelEx($vacancy     , 'position_level_slug'); ?>
                <?= $form->dropDownList($vacancy, 'position_level_slug', $positionLevels); ?>
            </div>

            <div class="row row-selects">
                <span class="error-place">
                    <?= $form->error($vacancy       , 'professional_specialization_id'); ?>
                </span>
                <?= $form->labelEx($vacancy       , 'professional_specialization_id'); ?>
                <?= $form->dropDownList($vacancy, 'professional_specialization_id', $specializations); ?>
            </div>

            <div class="row row-inputs">
                <span class="error-place">
                    <?= $form->error($vacancy       , 'label'); ?>
                </span>
                <?= $form->labelEx($vacancy  , 'label'); ?>
                <?= $form->textField($vacancy, 'label'); ?>
            </div>

            <div class="row">
                <span class="error-place">
                    <?= $form->error($vacancy    , 'link'); ?>
                </span>
                <?= $form->labelEx($vacancy  , 'link'); ?>
                <?= $form->textField($vacancy, 'link'); ?>
            </div>

            <div class="pull-content-center">
                <?php echo CHtml::submitButton($submitButtonTitle , [
                    'name' => 'add',
                    'class' => 'label background-dark-blue icon-circle-with-blue-arrow-big button-standard icon-padding-standard'
                ]); ?>
            </div>

            <!-- * Поля обязательные для заполнения-->

        <?php $this->endWidget(); ?>
</section>