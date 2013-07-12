<h2 class="thetitle text-center">Вы можете пройти демо-версию</h2>

<div class="form registrationform">
    <div class="transparent-boder">
        <div class="radiusthree yellowbg">
            <div class="registermessage registerpads">
                <a class="regicon icon-check" id="registration_check" href="#"><span style="display: none"><?php echo Yii::t('site', 'Выбрать');?></span></a>
                <h3>Демо-версия</h3>
                <div class="testtime"><strong>15</strong> Минут</div>
                <ul>
                    <li>Погружение в игровую среду для понимания, как работает симуляция</li>
                    <li>Знакомство с интерфейсами</li>
                    <li>Пример итогового отчёта по оценке навыков</li>
                </ul>
            </div>
        </div>
    </div>

    <h6 class="minititle" id="registration_hint" style="visibility: hidden"><?= Yii::t('site', 'Вы можете пройти симуляцию позже') ?></h6>

<?php $form = $this->beginWidget('CActiveForm', array(
	'id'                   => 'yum-user-registration-form',
	'enableAjaxValidation' => false,
)); ?>


    <div class="transparent-boder">
        <div class="row">
            <?php echo $form->textField($profile, 'email', ['placeholder' => $profile->getAttributeLabel('email')]); ?>
            <?php echo $form->error($profile    , 'email'); ?>
        </div>


        <div class="row">
            <?php echo $form->passwordField($user, 'password', ['placeholder' => $user->getAttributeLabel('password')]); ?>
            <?php echo $form->error($user        , 'password'); ?>
        </div>


        <div class="row">
            <?php echo $form->passwordField($user, 'password_again', ['placeholder' => $user->getAttributeLabel('password_again')]); ?>
            <?php echo $form->error($user        , 'password_again'); ?>
        </div>

        <div class="row" style="display: none">
            <?php echo $form->hiddenField($user, 'is_check', ['class' => 'registration_is_check']); ?>
        </div><div class="row">
            <?php echo CHtml::submitButton(Yii::t('site', 'Начать'), ['id'=>'registration_switch', 'data-next'=>Yii::t('site', 'Далее'), 'data-start'=>Yii::t('site', 'Начать')]); ?>
        </div></div><div class="reg terms-confirm"><?= $form->error($user, 'agree_with_terms'); ?><?= $form->checkBox($user, 'agree_with_terms', ['value' => 'yes', 'uncheckValue' => null]); ?>
        <?= $form->labelEx($user, 'agree_with_terms', ['label' => 'Я принимаю <a href="#" class="terms">Условия и Лицензионное соглашение</a>']); ?>
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
        var submit = $("#yum-user-registration-form input[type='submit']");
        var errors = $(".errorMessage");
        for (var i=0; i < errors.length;i++) {
            var inp = $(errors[i]).prev("input.error");
            $(inp).css({"border":"2px solid #bd2929"});
            $(errors[i]).addClass($(inp).attr("id"));
            $(submit).width($(submit).width()-2);
        }
    });
</script>