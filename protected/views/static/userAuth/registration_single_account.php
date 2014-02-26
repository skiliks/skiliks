
<section class="">

    <br/>
    <br/>
    <br/>
    <br/>

    <div class="nice-border column-2-3-fixed pull-center pull-content-center
        border-radius-standard background-yellow us-registration-window">
        <div class="us-registration-header"></div>
        <div class="us-registration-body">

            <h5 class="pull-center">Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию</h5>

            <br/>

            <!-- form form-with-red-errors registration-form -->
            <div class="pull-content-left">

                <?php $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'registration-form'
                )); ?>

                <div class="row <?= $this->hasErrors($form, $profile, 'email') ?>">
                    <span class="error-place">
                        <?= $form->error($profile, 'email'); ?>
                    </span>
                    <?php echo $form->labelEx($profile, 'Email', ['class' => 'padding-left-18']); ?>
                    <?php echo $form->textField($profile, 'email', ['placeholder' => 'Email']); ?>
                </div>

                <div class="row <?= $this->hasErrors($form, $profile, 'firstname') ?>
                    <?= $this->hasErrors($form, $profile, 'lastname') ?>">
                    <span class="error-place">
                        <span class="inline-error-first">
                            <?= $form->error($profile, 'firstname'); ?>
                        </span>
                        <?= $form->error($profile, 'lastname'); ?>
                    </span>
                    <?php echo $form->labelEx($profile, 'Имя', ['class' => 'padding-left-18']); ?>
                    <?php echo $form->textField($profile, 'firstname', ['placeholder' => 'Имя']); ?>
                    <?php echo $form->textField($profile, 'lastname', ['placeholder' => 'Фамилия', 'class' => 'margin-left-18']); ?>
                </div>

                <div class="row <?= $this->hasErrors($form, $user, 'password') ?>">
                    <span class="error-place">
                        <?php echo $form->error($user, 'password'); ?>
                    </span>
                    <?php echo $form->labelEx($user, 'password', ['class' => 'padding-left-18']); ?>
                    <?php echo $form->passwordField($user, 'password', ['class' => 'shifted']); ?>
                    <span class="action-toggle-show-password padding-left-18 inter-active-blue"> Показать пароль </span>
                </div>

                <div class="row margin-bottom-standard <?= $this->hasErrors($form, $user, 'password_again') ?>">
                    <span class="error-place">
                        <?php echo $form->error($user, 'password_again'); ?>
                    </span>
                    <?php echo $form->labelEx($user, Yii::t("site","Confirmation"), ['class' => 'padding-left-18']); ?>
                    <?php echo $form->passwordField($user, 'password_again', ['class' => 'shifted']); ?>
                </div>

                <div class="us-error-place <?= $this->hasErrors($form, $profile, 'not_activated') ?>">
                    <span class="error-place">
                        <?= str_replace(
                            '<a',
                            '<a class="inter-active-blue"',
                            $form->error($profile, 'not_activated')
                        ); ?>
                    </span>
                </div>

                <div class="row <?= $this->hasErrors($form, $user, 'agree_with_terms') ?>">
                    <span class="error-place">
                        <?php echo $form->error($user, 'agree_with_terms'); ?>
                    </span>
                    <label class="padding-left-18"></label>
                    <?= $form->checkBox($user, 'agree_with_terms', ['value' => 'yes', 'uncheckValue' => null]); ?>
                    <span class="us-i-agree-label inter-active">
                        Я принимаю
                            <span class="action-show-terms-pop-up inter-active-blue">
                                Условия и Лицензионное соглашение
                            </span>
                    </span>
                </div>

                <div class="row">
                    <label class="padding-left-18"></label>
                    <?php echo CHtml::submitButton( Yii::t('site', 'Sign up'), [
                        'class' => 'label background-dark-blue icon-circle-with-blue-arrow-big button-standard icon-padding-standard'
                    ]); ?>
                </div>

                <br/>

                <?php $this->endWidget(); ?>
            </div>
        </div>
    </div>
</section>

<?php $this->renderPartial('//global_partials/_popup_result_simulation_container', [ 'display_results_for' => $display_results_for]) ?>

<div class="clearfix"></div>