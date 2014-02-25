<section class="">

    <br/>
    <br/>
    <br/>
    <br/>

    <div class="nice-border column-2-3-fixed pull-center pull-content-center
        border-radius-standard background-yellow us-registration-window
        locator-corporate-invitations-list-box">
        <div class="us-registration-header"></div>
        <div class="us-registration-by-link-body">

            <h5 class="pull-center">Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию</h5>

            <br/>

            <!-- form form-with-red-errors registration-form -->
            <div class="pull-content-left">

                <?php $form = $this->beginWidget('CActiveForm', array(
                    'id' => 'registration-by-link-form'
                )); ?>

                <?= $form->error($this->user, 'username'); ?>
                <?= $form->error($profile, 'email'); ?>

                <div class="row <?= $this->hasErrors($form, $profile, 'firstname') ?>
                    <?= $this->hasErrors($form, $profile, 'lastname') ?>">
                    <span class="error-place">
                        <?= $form->error($profile, 'firstname'); ?>
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

                    <!-- decline-link -->
                    <a class="action-decline-invite padding-left-18 inter-active-blue"
                       data-invite-id="<?= $invite->id ?>">
                        Отказаться от приглашения
                    </a>
                </div>

                <br/>

                <?php $this->endWidget(); ?>
            </div>
        </div>
    </div>
</section>

<div class="clearfix"></div>

<!-- decline-form { -->
<div class="locator-invite-decline-box"></div>
<!-- decline-form } -->

<?php return ?>

<!-- ################################################################################################## -->



<h2 class="title"><?php echo Yii::t('site', 'Sign-up using your preferred option and get the results') ?></h2>

<section class="registration-by-link">
    <div class="text-right"><a href="/user/auth">Вход для зарегистрированных пользователей</a></div>
    <h1>Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию</h1>

    <div class="form form-with-red-errors registration-form ">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'registration-by-link-form'
        )); ?>

        <?= $form->error($this->user, 'username'); ?>
        <?= $form->error($profile, 'email'); ?>

        <div class="row wide">
            <?php echo $form->labelEx($profile, 'Имя'); ?>
            <?php echo $form->textField($profile, 'firstname', ['placeholder' => 'Имя', 'class' => 'shifted']); ?><?php echo $form->error($profile, 'firstname'); ?>
            <?php echo $form->textField($profile, 'lastname', ['placeholder' => 'Фамилия']); ?><?php echo $form->error($profile, 'lastname'); ?>
        </div>

        <div class="row wide">
            <?php echo $form->labelEx($account, 'professional_status_id'); ?>
            <?php echo $form->dropDownList($account, 'professional_status_id', $statuses, ['class' => 'shifted']); ?><?php echo $form->error($account, 'professional_status_id'); ?>
        </div>

        <div class="row wide">
            <?php echo $form->labelEx($user, 'password'); ?>
            <?php echo $form->passwordField($user, 'password', ['class' => 'shifted']); ?><?php echo $form->error($user, 'password'); ?>
        </div>

        <div class="row wide">
            <?php echo $form->labelEx($user, Yii::t("site","Confirmation")); ?>
            <?php echo $form->passwordField($user, 'password_again', ['class' => 'shifted']); ?><?php echo $form->error($user, 'password_again'); ?>
        </div>

        <div class="reg-by-link terms-confirm-left-align">
            <?= $form->error($user, 'agree_with_terms'); ?>
            <?= $form->checkBox($user, 'agree_with_terms', ['value' => 'yes', 'uncheckValue' => null]); ?>
            <?= $form->labelEx($user, 'agree_with_terms', ['label' => 'Я принимаю <a href="#" class="terms">Условия и Лицензионное соглашение</a>']); ?>
        </div><div class="row buttons">
            <button type="submit" class="blue-submit-button">
                <?= Yii::t("site","Sign up") ?>
            </button>

            <a class="decline-link">Отказаться от приглашения</a>
        </div>
        <?php $this->endWidget(); ?>
    </div>
</section>



<div class="blackout show"></div>
<style>
    #invite-decline-form{
        z-index:10;
        position: relative;
    }
</style>

