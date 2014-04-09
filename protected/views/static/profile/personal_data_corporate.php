
<section class="page-title-box column-full pull-content-center">
    <h1 class="margin-bottom-standard"><?php echo Yii::t('site', 'Profile') ?></h1>
</section>

<section class="pull-content-left nice-border reset-padding us-profile-width pull-center font-always-14px
    shadow-14 border-radius-standard background-transparent-20 unstandard-personal-data-height">

    <!--div class="transparent-boder profilewrap"-->
    <aside class="inline-block background-yellow border-radius-standard vertical-align-top">
        <?php $this->renderPartial('_menu_corporate', ['active' => ['personal-data' => true]]) ?>
    </aside>

    <section class="inline-block border-radius-standard background-F3FFFF
         pull-right pull-content-left vertical-align-top profile-right-side">
        <div class="">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'account-corporate-personal-form'
            )); ?>

            <div class="row <?= $this->hasErrors($form, $profile, 'firstname') ?> <?= $this->hasErrors($form, $profile, 'lastname') ?>">
                <span class="error-place">
                    <span class="unstandard-error-firstname">
                        <?php echo $form->error($profile, 'firstname'); ?>
                    </span>
                    <span class="unstandard-error-lastname">
                        <?php echo $form->error($profile, 'lastname'); ?>
                    </span>
                </span>

                <?php echo $form->labelEx($profile, 'Имя'); ?>

                <?php echo $form->textField($profile, 'firstname', [
                    'id'          => 'profile_firstname',
                    'placeholder' => 'Введите имя',
                    'style'       => 'margin-right: 25px;'
                ]); ?>

                <?php echo $form->textField($profile, 'lastname', [
                    'id'          => 'profile_lastname',
                    'placeholder' => 'Введите фамилию'
                ]); ?>
            </div>

            <div class="row" style="margin-top: 9px; font-size: 1.1em">
                <?php echo $form->labelEx($profile, 'Корпоративный емейл'); ?>
                <span class="value" style="font-family: ProximaNova-Bold; "><?php echo $profile->email; ?></span>
            </div>

            <div class="row cposwrap" style="margin-top: 10px;">
                <?php echo $form->labelEx($account , 'Должность'); ?>
                <span class="error-place">
                    <?php echo $form->error($account , 'position_id'); ?>
                </span>
                <?php echo $form->dropDownList($account, 'position_id', $positions); ?>
            </div>

            <div class="row buttons">
                <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), [
                    'name'  => 'save',
                    'class' => 'background-dark-blue icon-circle-with-blue-arrow-big button-standard icon-padding-standard',
                    'style' => 'margin-top: 22px;',
                ]); ?>
            </div>

            <?php $this->endWidget(); ?>
        </div>
    </section>
</section>
