<section class="page-title-box column-full pull-content-left ">
    <h1 class="bottom-margin-standard"><?php echo Yii::t('site', 'Profile') ?></h1>
</section>

<section class="pull-content-left nice-border reset-padding
    border-radius-standard background-transparent-20">

    <aside class="column-1-3 inline-block background-yellow border-radius-standard vertical-align-top">
        <?php $this->renderPartial('_menu_corporate', ['active' => ['password' => true]]) ?>
    </aside>

    <section class="column-2-3-wide inline-block border-radius-standard background-F3FFFF
         pull-right pull-content-left vertical-align-top profile-right-side">
        <div class="">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'account-corporate-password-form'
            )); ?>

            <div class="row">
                <p class="text16"><?php if($is_done) { echo Yii::t('site', 'Ваш новый пароль сохранён'); }else{ echo Yii::t('site', 'Вы можете изменить пароль'); } ?></p>
            </div>

            <div class="row <?= $this->hasErrors($form, $passwordForm, 'currentPassword') ?>">
                <span class="error-place">
                    <?php echo $form->error($passwordForm, 'currentPassword'); ?>
                </span>
                <?php echo $form->labelEx($passwordForm, 'currentPassword'); ?>
                <?php echo $form->passwordField($passwordForm, 'currentPassword'); ?>
            </div>

            <div class="row <?= $this->hasErrors($form, $passwordForm, 'password') ?>">
                <span class="error-place">
                    <?php echo $form->error($passwordForm, 'password'); ?>
                </span>
                <?php echo $form->labelEx($passwordForm, 'password'); ?>
                <?php echo $form->passwordField($passwordForm, 'password'); ?>
            </div>

            <div class="row <?= $this->hasErrors($form, $passwordForm, 'verifyPassword') ?>">
                <span class="error-place">
                    <?php echo $form->error($passwordForm, 'verifyPassword'); ?>
                </span>
                <?php echo $form->labelEx($passwordForm, 'verifyPassword'); ?>
                <?php echo $form->passwordField($passwordForm, 'verifyPassword'); ?>
            </div>
            <?php if($is_done) { ?>
                <div class="done-password-change"></div>
            <?php }else{ ?>
                <div class="row buttons">
                    <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), [
                        'name'  => 'save',
                        'class' => 'label background-dark-blue icon-circle-with-blue-arrow-big button-standard icon-padding-standard',
                    ]); ?>
                </div>
            <?php } ?>

            <?php $this->endWidget(); ?>
        </div>
    </section>
</section>

