<section class="partial">
    <label class="partial-label"><?= __FILE__ ?></label>

        <!--form invite-people-form sideform darkblueplacehld -->
        <div class="">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'invite-form'
            )); ?>

            <h2>Отправить приглашение</h2>

            <span class="form-global-errors">
                <?= $form->error($invite, 'invitations'); // You has no available invites! ?>
            </span>

            <div class="row <?= ($form->error($invite, 'firstname') || $form->error($invite, 'lastname')) ? 'error' : ''; ?>">
                <span class="error-place">
                    <?php /* нет смысла делать класс ради одного такого случая */ ?>
                    <span class="unstandard-firstname-error">
                        <?= $form->error($invite, 'firstname'); ?>
                    </span>
                    <?= $form->error($invite, 'lastname'); ?>
                </span>
                <?= $form->labelEx($invite, 'full_name'); ?>
                <?= $form->textField($invite, 'firstname', ['placeholder' => Yii::t('site','First name')]); ?>
                <?= $form->textField($invite, 'lastname', ['placeholder'  => Yii::t('site','Last Name')]); ?>

            </div>

            <div class="row <?= ($form->error($invite, 'email')) ? 'error' : ''; ?>">
                <span class="error-place">
                    <?= $form->error($invite, 'email'); ?>
                </span>
                <?= $form->labelEx($invite, 'email'); ?>
                <?= $form->textField($invite, 'email', ['placeholder' => Yii::t('site','Enter Email address')]); ?>
            </div>

            <div class="row unstandard-position-error wide <?= (0 == count($vacancies) ? 'no-border' : '') ?> <?= ($form->error($invite, 'vacancy_id')) ? 'error' : ''; ?>">
                <span class="error-place">
                    <?= $form->error($invite, 'vacancy_id'); ?>
                </span>
                <?= $form->labelEx($invite, 'vacancy_id'); ?>
                <?= $form->dropDownList($invite, 'vacancy_id', $vacancies); ?>

                <span class="action-add-vacancy button-add-vacancy"></span>
            </div>

            <div class="row">
                <?= CHtml::submitButton(
                    'Отправить',
                    [
                        'name' => 'prevalidate',
                        'class' => 'button-white inter-active label icon-arrow-blue'
                    ]);
                ?>
            </div>

            <?php $this->endWidget(); ?>
        </div>

        <?php // add_vacancy_form { ?>
            <div class="form locator-form-vacancy" style="display: none;">
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
</section>