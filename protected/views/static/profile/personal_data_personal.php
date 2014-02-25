<section class="page-title-box column-full pull-content-left ">
    <h1 class="margin-bottom-standard"><?php echo Yii::t('site', 'Profile') ?></h1>
</section>

<section class="pull-content-left nice-border reset-padding unstandard-personal-dat-height
    border-radius-standard background-transparent-20">

    <!--div class="transparent-boder profilewrap"-->
    <aside class="column-1-3 inline-block background-yellow border-radius-standard vertical-align-top">
        <?php $this->renderPartial('_menu_personal', ['active' => ['personal-data' => true]]) ?>
    </aside>

    <section class="column-2-3-fixed inline-block border-radius-standard background-F3FFFF
         pull-right pull-content-left vertical-align-top profile-right-side">
        <div class="">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'account-personal-personal-form'
            )); ?>

            <div class="row <?= $this->hasErrors($form, $profile, 'firstname') ?> <?= $this->hasErrors($form, $profile, 'lastname') ?>">
                <span class="error-place">
                       <span class="unstandard-error-firstname">
                        <?php echo $form->error($profile, 'firstname'); ?>
                    </span>
                    <span class="unstandard-error-firstname">
                        <?php echo $form->error($profile, 'lastname'); ?>
                    </span>
                </span>
                <?php echo $form->labelEx($profile, 'firstname'); ?>
                <?php echo $form->textField($profile, 'firstname'); ?>
                <?php echo $form->textField($profile, 'lastname'); ?>
            </div>

            <div class="row rowup">
                <?php echo $form->labelEx($profile , 'Email'); ?>
                <span class="value"><?php echo $profile->email; ?></span>
            </div>

            <div class="row <?= $this->hasErrors($form, $account, 'professional_status_id') ?>">
                <span class="error-place">
                    <?php echo $form->error($account, 'professional_status_id'); ?>
                </span>
                <?php echo $form->labelEx($account     , 'professional_status_id'); ?>
                <?php echo $form->dropDownList($account, 'professional_status_id', $statuses); ?>
            </div>

            <div class="row <?= $this->hasErrors($form, $account, 'professional_status_id') ?>">
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
                <?php echo $form->textField($account, 'birthday[day]', array('value'=>$account->getBirthdayDate('d'), 'placeholder'=>'ДД')); ?>
                <?php echo $form->textField($account, 'birthday[month]', array('value'=>$account->getBirthdayDate('m'), 'placeholder'=>'ММ')); ?>
                <?php echo $form->textField($account, 'birthday[year]', array('value'=>$account->getBirthdayDate('Y'), 'placeholder'=>'ГГГГ')); ?>
            </div>

            <div class="row <?= $this->hasErrors($form, $account, 'location') ?>">
                <span class="error-place">
                    <?php echo $form->error($account, 'location'); ?>
                </span>
                <?php echo $form->labelEx($account, 'location'); ?>
                <?php echo $form->textField($account, 'location'); ?>
            </div>

            <div class="row buttons">
                <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), [
                    'name'  => 'save',
                    'class' => 'label background-dark-blue icon-circle-with-blue-arrow-big button-standard icon-padding-standard'
                ]); ?>
            </div>

            <?php $this->endWidget(); ?>

        </div>
    </section>
</section>

<div class="clearfix"></div>

