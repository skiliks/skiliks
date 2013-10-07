<script type="text/javascript">$(function () {
    $('input').focus(function () {
        $('.form').removeClass('active');
        $(this).parents('.form').addClass('active');
    })
})</script>
<section class="registration">
	<h2 class="shorter-title"><?php echo empty($simPassed) ? 'Зарегистрируйтесь, выбрав подходящий профиль' : 'Зарегистрируйтесь, выбрав подходящий профиль, и получите пример отчёта' ?></h2>
	<div class="form form-account-personal">
        <a class="regicon icon-chooce registration_check" href="#">
            <span style="display: block">
                <?php echo Yii::t('site', 'Выбрать');?>
            </span>
        </a>
	    <h1>Индивидуальный<br>профиль</h1>
        <p class="p-chose-accaount-type">(Вы - сотрудник или соискатель)</p>
        <ul>
			<li><?php echo Yii::t('site', 'Полная оценка навыков бесплатно') ?></li>
			<li><?php echo Yii::t('site', 'Skills comparison with others') ?>*</li>
		</ul>
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id'                   => 'yum-user-registration-form',
            'enableAjaxValidation' => false,
        )); ?>
        <div class="row">
            <?php echo $form->error($accountPersonal, 'professional_status_id'); ?>
            <div class="field">
                <?php echo $form->labelEx($accountPersonal     ,'professional_status_id'); ?>
                <?php echo $form->dropDownList($accountPersonal,'professional_status_id', $statuses); ?>
            </div>
        </div>
        <div class="row"></div>
	</div>
	<!-- --------------------------------------------------------------------------------------------------------- -->
	<div class="form form-account-corporate">
        <a class="regicon icon-check registration_check" href="#">
            <span style="display: none">
                <?php echo Yii::t('site', 'Выбрать');?>
            </span>
        </a>
	    <h1>Корпоративный<br>профиль</h1>
        <p class="p-chose-accaount-type">(Вы - работодатель)</p>
	    <ul>
			<li><?php echo Yii::t('site', 'Package of simulations to assess others') ?></li>
			<li><?php echo Yii::t('site', 'Simple but powerful tool for assessment process') ?></li>
			<li><?php echo Yii::t('site', 'Comprehensive statistics on people and skills') ?>*</li>
		</ul>
	    <div class="row">
            <?php if ($isCorporateSubmitted): ?>
                <?php echo $form->error($accountCorporate, 'industry_id'); ?>
            <?php endif ?>
            <div class="field">
                <?php echo $form->labelEx($accountCorporate     , 'industry_id'); ?>
    	        <?php echo $form->dropDownList($accountCorporate, 'industry_id', $industries); ?>
            </div>
	    </div>
	</div>
	<!-- --------------------------------------------------------------------------------------------------------- -->
	<div style="clear:both;"></div>
</section>
<div class="form registrationform">
    <div class="transparent-boder">
        <div class="row one-part">
            <div class="row two-parts">
                <?php echo $form->textField($profile, 'firstname', ['placeholder' => $profile->getAttributeLabel('firstname')]); ?>
                <?php echo $form->error($profile, 'firstname'); ?>
            </div>

            <div class="row two-parts to-right">
                <?php echo $form->textField($profile, 'lastname', ['placeholder' => $profile->getAttributeLabel('lastname')]); ?>
                <?php echo $form->error($profile, 'lastname'); ?>
            </div>
        </div>
        <div class="row one-part to-left">
            <div class="row four-parts email-input">
                <?php echo $form->textField($profile, 'email', ['placeholder' => $profile->getAttributeLabel('email')]); ?>
                <?php echo $form->error($profile, 'email'); ?>
            </div>

            <div class="row four-parts to-right password-input">
                <?php echo $form->passwordField($user, 'password', ['placeholder' => $user->getAttributeLabel('password')]); ?>
                <?php echo $form->error($user, 'password'); ?>
            </div>

            <div class="row four-parts to-right password-again-input">
                <?php echo $form->passwordField($user, 'password_again', ['placeholder' => $user->getAttributeLabel('password_again')]); ?>
                <?php echo $form->error($user, 'password_again'); ?>
            </div>
            <div class="row" style="display: none">
                <?php echo $form->hiddenField($user, 'is_check', ['class' => 'registration_is_check']); ?>
            </div>
            <div class="row" id="account-type" style="display: none">
                <input type="hidden" value="corporate" name="account-type">
            </div>
            <div class="row four-parts to-right submit-input">
                <?php echo CHtml::submitButton(Yii::t('site', ' Зарегистрироваться'), ['id'=>'registration_switch']); ?>
            </div>
        </div>
        <div style="clear:both;"></div>
        <?php  echo $form->error($user, 'general_error', ['class'=>'errorMessage general_error']); ?>
        <?php //echo $form->error($profile, 'general_error', ['style'=>'position:static; display:inline-block; margin-top:5px; float:left;']); ?>

    </div>

    <div class="reg terms-confirm"><?= $form->error($user, 'agree_with_terms'); ?><?= $form->checkBox($user, 'agree_with_terms', ['value' => 'yes', 'uncheckValue' => null]); ?>
        <?= $form->labelEx($user, 'agree_with_terms', ['label' => 'Я принимаю <a href="#" class="terms">Условия и Лицензионное соглашение</a>']); ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
