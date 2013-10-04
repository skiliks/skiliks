<section class="registration-by-link">
    <div class="text-right"></div>
    <h1 style="text-align: center; width: 100%;">Создание корпоративного профиля</h1>

    <div class="form" style="min-height:680px; ">

        <?php
        /** @var CActiveForm $form */
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'referrer-registration-form',
            'htmlOptions' => ['class' => 'payment-form'],
            'action' => '/register-referal/'.$refId,
            'enableAjaxValidation' => true,
            'clientOptions' => [
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'afterValidate'    => 'js:referralRegistration',
            ]
        ));
        ?>

        <?= $form->error($this->user, 'username'); ?>
        <?= $form->error($profile, 'email'); ?>

        <div class="row" style="margin-top:-40px;">
            <?php echo $form->labelEx($profile, 'Имя'); ?>
            <?php echo $form->textField($profile, 'firstname', ['placeholder' => 'Имя']); ?>
            <?php echo $form->error($profile, 'firstname'); ?>
            <?php echo $form->textField($profile, 'lastname', ['placeholder' => 'Фамилия']); ?>
            <?php echo $form->error($profile, 'lastname'); ?>
        </div>

        <div class="row" style="margin-top:-35px;">
            <?php echo $form->labelEx($profile, 'Пароль'); ?>
            <?php echo $form->passwordField($user, 'password'); ?>
            <?php echo $form->error($user, 'password'); ?>
        </div>

        <div class="row" style="margin-top:-35px;">
            <?php echo $form->labelEx($profile, 'Подтверждение'); ?>
            <?php echo $form->passwordField($user, 'password_again'); ?>
            <?php echo $form->error($user, 'password_again'); ?>
        </div>

        <div class="row wide" style="margin-top:-35px;">
            <?php echo $form->labelEx($accountCorporate, 'industry_id'); ?>
            <?php echo $form->dropDownList($accountCorporate, 'industry_id', $industries); ?>
            <?php echo $form->error($accountCorporate, 'industry_id'); ?>
        </div>
        <div class="reg-by-link terms-confirm" style="margin-top:-55px;">
            <?= $form->checkBox($user, 'agree_with_terms', ['value' => 'yes', 'uncheckValue' => null]); ?>
            <?= $form->labelEx($user, 'agree_with_terms', ['label' => 'Я принимаю <a href="#" class="terms">Условия и Лицензионное соглашение</a>']); ?>
            <?= $form->error($user, 'agree_with_terms'); ?>
        </div>
        <div class="row buttons" style="margin-top:-40px;">
            <?php echo CHtml::submitButton(Yii::t("site","Sign up")); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</section>