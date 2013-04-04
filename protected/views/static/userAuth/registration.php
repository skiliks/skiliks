<h2 class="thetitle text-center">Вы можете <span>Бесплатно</span> пройти пробную версию </h2>

<div class="form registrationform">
    <div class="transparent-boder">
        <div class="radiusthree yellowbg">
            <div class="registermessage registerpads">
                <a class="regicon icon-check" id="registration_check" href="/simulation/promo/2"><span style="display: none"><?php echo Yii::t('site', 'Выбрать');?></span></a>
                <h3>Пробный тест</h3>
                <div class="testtime"><strong>45</strong> Минут</div>
                <ul>
                    <li>Частичная оценка навыков бесплатно</li>
                    <li>Погружение в игровую среду для понимания, как работает симуляция</li>
                    <li>Опыт прохождения теста</li>
                </ul>
            </div>
        </div>
    </div>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id'                   => 'yum-user-registration-form',
	'enableAjaxValidation' => false,
)); ?>


    <div class="transparent-boder">
        <div class="row">
            <?php echo $form->textField($profile, 'email', ['placeholder' => $profile->getAttributeLabel('Введите Ваш email')]); ?>
            <span class="errorMessageWrap">
                <?php echo $form->error($profile    , 'email'); ?>
            </span>
        </div>


        <div class="row">
            <?php echo $form->passwordField($user, 'password', ['placeholder' => $profile->getAttributeLabel('Введите пароль')]); ?>
            <span class="errorMessageWrap">
                <?php echo $form->error($user        , 'password'); ?>
            </span>
        </div>


        <div class="row">
            <?php echo $form->passwordField($user, 'password_again', ['placeholder' => $profile->getAttributeLabel('Подтвердите пароль')]); ?>
            <span class="errorMessageWrap">
                <?php echo $form->error($user        , 'password_again'); ?>
            </span>
        </div>

        <div class="row" style="display: none">
            <?php echo $form->hiddenField($user, 'is_check', ['class' => 'registration_is_check']); ?>
        </div>


    <!--<a id="pass-switcher"><?php echo Yii::t('site', 'Show passwords') ?></a>

    <p class="note"><?php echo Yii::t('site', 'Fields with * are required.') ?></p>-->

        <div class="row">
            <?php echo CHtml::submitButton(Yii::t('site', 'Начать')); ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="errorlongMessage">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<div style="float: none; clear: both; height: 100px; width: 100%;"></div>

<script type="text/javascript">
    $('#pass-switcher').click(function(event){
        // show * or password letters switcher {
        event.preventDefault();

        var passInput = $('#YumUser_password');
        var passAgainInput = $('#YumUser_password_again');

        if ('password' == passInput.attr('type')) {
            passInput.clone().attr('type','text').insertAfter(passInput).prev().remove();
        } else {
            passInput.clone().attr('type','password').insertAfter(passInput).prev().remove();
        }

        if ('password' == passAgainInput.attr('type')) {
            passAgainInput.clone().attr('type','text').insertAfter(passAgainInput).prev().remove();
        } else {
            passAgainInput.clone().attr('type','password').insertAfter(passAgainInput).prev().remove();
        }
        // show * or password letters switcher }
    });

    $(document).ready(function(){
        var error = $(".errorMessageWrap");
        for (var i=0; i < error.length; i++) {
            if ($(error[i]).html().match(/\S+/)) {
                var inp = $(error[i]).prev("input").width();
                var top = $(error[i]).height();
                $(error[i]).css("max-width",inp+35);
                $(error[i]).css("top",-top);
            }
        }
    });
</script>