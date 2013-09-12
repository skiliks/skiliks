<div class="pad-large">

    <h3>Отправить приглашение</h3>

    <div class="form-simple form-small">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'invite-form'
        )); ?>

    <span class="form-global-errors">
        <?php echo $form->error($invite, 'invitations'); // You have no available invites! ?>
    </span>

    <div class="row <?php echo ($form->error($invite, 'firstname') || $form->error($invite, 'lastname')) ? 'error' : ''; ?>"><?php echo $form->labelEx($invite, 'full_name'); ?><?php echo $form->textField($invite, 'firstname', ['placeholder' => Yii::t('site','First name')]); ?><?php echo $form->error($invite, 'firstname'); ?><?php echo $form->textField($invite, 'lastname', ['placeholder'  => Yii::t('site','Last Name')]); ?><?php echo $form->error($invite, 'lastname'); ?>
    </div>

    <div class="row <?php echo ($form->error($invite, 'email')) ? 'error' : ''; ?>">
        <?php echo $form->labelEx($invite, 'email'); ?><?php echo $form->textField($invite, 'email', ['placeholder' => Yii::t('site','Enter Email address')]); ?><?php echo $form->error($invite, 'email'); ?>
    </div>

    <div class="row wide <?php echo (0 == count($vacancies) ? 'no-border' : '') ?> <?php echo ($form->error($invite, 'vacancy_id')) ? 'error' : ''; ?>v">
        <?php echo $form->labelEx($invite, 'vacancy_id'); ?><?php echo $form->dropDownList($invite, 'vacancy_id', $vacancies); ?><?php echo $form->error($invite, 'vacancy_id'); ?>
        <span id="corporate-dashboard-add-vacancy" class="btn-add"></span>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Отправить', ['name' => 'prevalidate', 'class' => 'btn btn-primary']); ?>
    </div>

    <?php $this->endWidget(); ?>
    </div>
</div>

<?php // add_vacancy_form { ?>
    <div class="form form-vacancy" style="display: none;">
        <?php $this->renderPartial('//global_partials/_add_vacancy_form', [
            'h1'              => 'Добавить позицию',
            'dataUrl'         => '/dashboard',
            'vacancy'         => new Vacancy(),
            'positionLevels'  => StaticSiteTools::formatValuesArrayLite(
                    'PositionLevel',
                    'slug',
                    'label',
                    '',
                    'Выберите уровень позиции'
                ),
            'specializations' => $specializations = StaticSiteTools::formatValuesArrayLite(
                'ProfessionalSpecialization',
                'id',
                'label',
                "",
                'Выберите специализацию'
            ),
        ]) ?>
    </div>
<?php // add_vacancy_form } ?>

