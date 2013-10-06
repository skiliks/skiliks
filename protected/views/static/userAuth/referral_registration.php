
<section class="registration-by-link">
    <div class="text-right"></div>
    <h1 class="ProximaNova" style="text-align: center; width: 100%;">Создание корпоративного профиля</h1>

    <div class="form form-with-red-errors registration-form" style="min-height:680px; padding-top: 20px;">

        <?php
        /** @var CActiveForm $form */
        $form = $this->beginWidget('CActiveForm', array(
            'id' => 'referrer-registration-form',
            'htmlOptions' => ['class' => 'payment-form'],
            'action' => '/register-referral/'.$refId,
            'enableAjaxValidation' => false,
            'clientOptions' => [
                'validateOnSubmit' => true,
                'validateOnChange' => false,
                'afterValidate'    => 'js:referralRegistration',
            ]
        ));
        ?>

        <div class="row wide" style="margin-top:-40px;">
            <?php echo $form->labelEx($profile, 'Имя'); ?>
            <?php echo $form->textField($profile, 'firstname', ['placeholder' => 'Имя']); ?><?php echo $form->error($profile, 'firstname'); ?>
            <?= $form->error($user, 'username'); ?><span class="email-error"><?= $form->error($profile, 'email'); ?></span>
            <?php echo $form->textField($profile, 'lastname', ['placeholder' => 'Фамилия']); ?><?php echo $form->error($profile, 'lastname'); ?>
        </div><div class="row wide">
            <?php echo $form->labelEx($profile, 'Пароль'); ?>
            <?php echo $form->passwordField($user, 'password'); ?><?php echo $form->error($user, 'password'); ?>
        </div><div class="row wide">
            <?php echo $form->labelEx($profile, 'Подтверждение'); ?>
            <?php echo $form->passwordField($user, 'password_again'); ?><?php echo $form->error($user, 'password_again'); ?>
        </div><div class="row wide">
            <?php echo $form->labelEx($accountCorporate, 'industry_id'); ?>
            <?php echo $form->dropDownList($accountCorporate, 'industry_id', $industries); ?>
            <?php echo $form->error($accountCorporate, 'industry_id'); ?>
        </div><div class="reg-by-link terms-confirm-left-align">
            <?= $form->error($user, 'agree_with_terms'); ?>
            <?= $form->checkBox($user, 'agree_with_terms', ['value' => 'yes', 'uncheckValue' => null]); ?>
            <?= $form->labelEx($user, 'agree_with_terms', ['label' => 'Я принимаю <a href="#" class="terms">Условия и Лицензионное соглашение</a>']); ?>
        </div><div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t("site","Sign up"), ['class' => 'ProximaNova-Bold']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</section>

<div class="blackout show"></div>

<script type="text/javascript">
    // add "blackout"
    $(".blackout").prependTo("body");

    $(document).ready(function(){
        var errors = $(".errorMessage");
        console.log(errors);
        for (var i=0; i < errors.length;i++) {
            var sbHolder = $(errors[i]).parent().find('.sbHolder');
            console.log(sbHolder);
            sbHolder.addClass("sbHolder_error");
        }
    });
</script>

<style>
    <?php // todo: make really sticky footer ?>
    .footer {
        position: relative;
    }
</style>