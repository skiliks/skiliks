

<h1>
    <?= (null !== $vacancy->id) ? 'Редактирование' : 'Добавление новой' ?>
     позиции в аккаунте <?= $user->profile->email ?></h1>

<br/>
<br/>

<a href="/admin_area/user/<?= $user->id ?>/vacancies-list">
    &lt;- Вернуться назад
</a>

<br/>
<br/>
<br/>
<br/>

<?php $form = $this->beginWidget('CActiveForm', [
        'id'          => 'add-vacancy-admin-form',
        'htmlOptions' => [
            'class'    => 'form-horizontal',
        ],
        'action'      => '/admin_area/user/' . $user->id . '/vacancy/add',
    ]);
?>

    <!-- Скрытые поля -->

    <?php if (null !== $vacancy->id): ?>
        <?= CHtml::hiddenField('add', true) ?>
        <?= CHtml::hiddenField('id', $vacancy->id) ?>
    <?php endif ?>

    <!-- Отрасль -->

    <div class="control-group <?= $this->hasErrors($form, $vacancy, 'professional_occupation_id') ?>">
        <?= $form->labelEx($vacancy , 'professional_occupation_id', ['class' => 'control-label']); ?>

        <div class="controls">
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

        <span class="help-inline">
            <?= $form->error($vacancy       , 'professional_occupation_id'); ?>
        </span>
    </div>

    <!-- Уровень позиции -->

    <div class="control-group <?= $this->hasErrors($form, $vacancy, 'position_level_slug') ?>">
        <?= $form->labelEx($vacancy , 'position_level_slug', ['class' => 'control-label']); ?>

        <div class="controls">
            <?= $form->dropDownList($vacancy, 'position_level_slug', $positionLevels); ?>
        </div>

        <span class="help-inline">
            <?= $form->error($vacancy , 'position_level_slug'); ?>
        </span>
    </div>

    <!-- Специализация -->

    <div class="control-group <?= $this->hasErrors($form, $vacancy, 'professional_specialization_id') ?>">
        <?= $form->labelEx($vacancy , 'professional_specialization_id', ['class' => 'control-label']); ?>

        <div class="controls">
            <?= $form->dropDownList($vacancy, 'professional_specialization_id', $specializations); ?>
        </div>

        <span class="help-inline">
            <?= $form->error($vacancy , 'professional_specialization_id'); ?>
        </span>
    </div>

    <!-- Название вакансии -->

    <div class="control-group <?= $this->hasErrors($form, $vacancy, 'label') ?>">
        <?= $form->labelEx($vacancy  , 'label', ['class' => 'control-label']); ?>

        <div class="controls">
            <?= $form->textField($vacancy, 'label'); ?>
        </div>

        <span class="help-inline">
            <?= $form->error($vacancy , 'label'); ?>
        </span>
    </div>

    <!-- Ссылка -->

    <div class="control-group <?= $this->hasErrors($form, $vacancy, 'link') ?>">
        <?= $form->labelEx($vacancy  , 'link', ['class' => 'control-label']); ?>

        <div class="controls">
            <?= $form->textField($vacancy, 'link', ['class' => 'span5']); ?>
        </div>

        <span class="help-inline">
            <?= $form->error($vacancy , 'link'); ?>
        </span>
    </div>

    <div class="form-actions">
        <?php echo CHtml::submitButton('Сохранить' , [
            'name'  => 'action',
            'class' => 'btn btn-success'
        ]); ?>
    </div>

<?php $this->endWidget(); ?>








