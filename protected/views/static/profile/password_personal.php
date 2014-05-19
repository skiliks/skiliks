
<section class="page-title-box column-full pull-content-center">
    <h1 class="margin-bottom-standard"><?php echo Yii::t('site', 'Profile') ?></h1>
</section>

<section class="pull-content-left nice-border reset-padding us-password-personal-height us-profile-width
    shadow-14 border-radius-standard background-transparent-20 pull-center font-always-14px">

    <aside class="inline-block background-yellow border-radius-standard vertical-align-top">
        <?php $this->renderPartial('_menu_personal', ['active' => ['password' => true]]) ?>
    </aside>

    <section class="inline-block border-radius-standard background-F3FFFF
         pull-right pull-content-left vertical-align-top profile-right-side">
        <div class="">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'account-corporate-password-form'
            )); ?>

            <div class="row">
                <p class="text16" style="font-size: 1.1em">
                    <?php if ($is_done) : ?>
                        <?= Yii::t('site', 'Ваш новый пароль сохранён') ?>
                    <?php else : ?>
                        <?= Yii::t('site', 'Вы можете установить пароль') ?>
                    <?php endif; ?>
                </p>
            </div>

            <div class="row <?= $this->hasErrors($form, $passwordForm, 'currentPassword') ?>">
                <span class="error-place">
                    <?php echo $form->error($passwordForm, 'currentPassword'); ?>
                </span>
                <?php echo $form->labelEx($passwordForm, 'Текущий пароль'); ?>
                <?php echo $form->passwordField($passwordForm, 'currentPassword'); ?>
            </div>

            <div class="row <?= $this->hasErrors($form, $passwordForm, 'password') ?>"
                 style="margin-top: 10px;">
                <span class="error-place">
                    <?php echo $form->error($passwordForm, 'password'); ?>
                </span>
                <?php echo $form->labelEx($passwordForm, 'Новый пароль'); ?>
                <?php echo $form->passwordField($passwordForm, 'password'); ?>
            </div>

            <div class="row <?= $this->hasErrors($form, $passwordForm, 'verifyPassword') ?>"
                 style="margin-top: 10px; margin-bottom: 10px;">
                <span class="error-place">
                    <?php echo $form->error($passwordForm, 'verifyPassword'); ?>
                </span>
                <?php echo $form->labelEx($passwordForm, 'Повторите пароль'); ?>
                <?php echo $form->passwordField($passwordForm, 'verifyPassword'); ?>
            </div>

            <?php if($is_done) { ?>
                <div class="done-password-change"></div>
            <?php }else{ ?>
                <div class="row buttons">
                    <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), [
                        'name'  => 'save',
                        'class' => 'background-dark-blue icon-circle-with-blue-arrow-big button-standard icon-padding-standard',
                    ]); ?>
                </div>
            <?php } ?>

            <?php $this->endWidget(); ?>
        </div>
    </section>
</section>