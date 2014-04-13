
<section class="page-title-box column-full pull-content-center">
    <h1 class="margin-bottom-standard"><?php echo Yii::t('site', 'Profile') ?></h1>
</section>

<section class="pull-content-left nice-border reset-padding unstandard-personal-data-height us-profile-width font-always-14px
    shadow-14 border-radius-standard background-transparent-20 pull-center">

    <!--div class="transparent-boder profilewrap"-->
    <aside class="inline-block background-yellow border-radius-standard vertical-align-top">
        <?php $this->renderPartial('_menu_personal', ['active' => ['personal-data' => true]]) ?>
    </aside>

    <section class="inline-block border-radius-standard background-F3FFFF
         pull-right pull-content-left vertical-align-top profile-right-side">
        <div class="">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'account-personal-personal-form'
            )); ?>

            <div class="row <?= $this->hasErrors($form, $profile, 'firstname') ?> <?= $this->hasErrors($form, $profile, 'lastname') ?>"
                style="margin-top: 6px;">
                <span class="error-place">
                       <span class="unstandard-error-firstname">
                        <?php echo $form->error($profile, 'firstname'); ?>
                    </span>
                    <span class="unstandard-error-firstname" style="margin-left: 25px;">
                        <?php echo $form->error($profile, 'lastname'); ?>
                    </span>
                </span>
                <?php echo $form->labelEx($profile, 'firstname'); ?>
                <?php echo $form->textField($profile, 'firstname', ['placeholder' => 'Введите имя']); ?>
                <?php echo $form->textField($profile, 'lastname', ['placeholder' => 'Введите фамилию']); ?>
            </div>

            <div class="row rowup">
                <?php echo $form->labelEx($profile , 'Email'); ?>
                <span class="value" style="font-family: ProximaNova-Bold;"><?php echo $profile->email; ?></span>
            </div>

            <div class="row <?= $this->hasErrors($form, $account, 'professional_status_id') ?>"
                style="margin-top: 8px;">
                <span class="error-place">
                    <?php echo $form->error($account, 'professional_status_id'); ?>
                </span>
                <?php echo $form->labelEx($account     , 'professional_status_id', [
                    'style' => 'line-height: 1em;'
                ]); ?>
                <span style="vertical-align: top;">
                    <?php echo $form->dropDownList($account, 'professional_status_id', $statuses); ?>
                </span>
            </div>

            <div class="row <?= $this->hasErrors($form, $account, 'industry_id') ?> margin-bottom-half-standard"
                style="margin-top: -4px;">
                <span class="error-place">
                    <?php echo $form->error($account       , 'industry_id'); ?>
                </span>
                <?php echo $form->labelEx($account     , 'industry_id'); ?>
                <?php echo $form->dropDownList($account, 'industry_id', $industries); ?>
            </div>

            <div class="row unstandart-triple-input <?= $this->hasErrors($form, $account, 'birthday') ?>">
                <span class="error-place">
                    <?php echo $form->error($account, 'birthday'); ?>
                </span>
                <?php echo $form->labelEx($account, 'birthday'); ?>
                <?php echo $form->textField($account, 'birthday[day]', [
                    'maxlength'        => 2,
                    'value'       => $account->getBirthdayDate('d'),
                    'placeholder' => 'ДД',
                    'style'       => 'width: 34px;',
                ]); ?>
                <?php echo $form->textField($account, 'birthday[month]', [
                    'maxlength'        => 2,
                    'value'       => $account->getBirthdayDate('m'),
                    'placeholder' => 'ММ',
                    'style'       => 'width: 34px; margin-left: 10px;',
                ]); ?>
                <?php echo $form->textField($account, 'birthday[year]', [
                    'maxlength'        => 4,
                    'value'       => $account->getBirthdayDate('Y'),
                    'placeholder' => 'ГГГГ',
                    'style'       => 'width: 55px; margin-left: 10px;',
                ]); ?>
            </div>

            <div class="row <?= $this->hasErrors($form, $account, 'location') ?>"
                style="margin-top: 13px;">
                <span class="error-place">
                    <?php echo $form->error($account, 'location'); ?>
                </span>
                <?php echo $form->labelEx($account, 'location'); ?>
                <?php echo $form->textField($account, 'location'); ?>
            </div>

            <div class="row buttons" style="margin-top: 13px;">
                <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), [
                    'name'  => 'save',
                    'class' => 'background-dark-blue icon-circle-with-blue-arrow-big button-standard icon-padding-standard'
                ]); ?>
            </div>

            <?php $this->endWidget(); ?>

        </div>
    </section>
</section>

<div class="clearfix"></div>

