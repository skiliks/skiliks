<script type="text/javascript">$(function () {
    $('input').focus(function () {
        $('.form').removeClass('active');
        $(this).parents('.form').addClass('active');
    })
})</script>
<section class="registration">
	<h2 class="shorter-title"><?php echo empty($simPassed) ? 'Зарегистрируйтесь, выбрав подходящий профиль' : 'Зарегистрируйтесь, выбрав подходящий профиль, и получите пример отчёта' ?></h2>
	<div class="form form-account-personal">
	    <h1><?php echo Yii::t('site', 'Personal account') ?></h1>
        <p class="ProximaNova-Bold-22px p-chose-accaount-type">(Вы - сотрудник или соискатель)</p>
        <ul>
			<li><?php echo Yii::t('site', 'Полная оценка навыков бесплатно') ?></li>
			<li><?php echo Yii::t('site', 'Skills comparison with others') ?>*</li>
		</ul>
	    <?php $form = $this->beginWidget('CActiveForm', array(
	        'id'                   => 'user-account-personal-form',
	        'enableAjaxValidation' => false,
	    )); ?>
        <div class="row wide">
                <?php echo $form->error($profilePersonal, 'email'); ?>
            <div class="field">
                <?php echo $form->labelEx($profilePersonal, 'Email'); ?>
                <?php echo $form->textField($profilePersonal, 'email', ['placeholder' => $profilePersonal->getAttributeLabel('email'), 'class' => $isCorporateSubmitted ? 'account-submitted' : '']); ?>
            </div>
        </div>
	    <div class="row">
                 <?php echo $form->error($userPersonal, 'password'); ?>
                 <?php echo $form->error($userPersonal, 'password_again', ['class' => 'errorMessage right']); ?>
            <div class="field">
                <?php echo $form->labelEx($userPersonal  , Yii::t('site', 'Password')); ?>
                <?php echo $form->passwordField($userPersonal, 'password', ['placeholder' => Yii::t('site', 'Password'), 'class' => $isCorporateSubmitted ? 'account-submitted' : '']); ?>
                <?php echo $form->passwordField($userPersonal, 'password_again', ['placeholder' => $userPersonal->getAttributeLabel('password_again'), 'class' => $isCorporateSubmitted ? 'account-submitted' : '']); ?>
            </div>
                <?php echo $form->error($profilePersonal    , 'firstname'); ?>
                <?php echo $form->error($profilePersonal    , 'lastname', ['class' => 'errorMessage right']); ?>
            <div class="field">
	        <?php echo $form->labelEx($profilePersonal  , 'Name'); ?>
	        <?php echo $form->textField($profilePersonal, 'firstname',['placeholder' => Yii::t('site', 'First name'), 'class' => $isPersonalSubmitted ? 'account-submitted' : '']); ?>
	        <?php echo $form->textField($profilePersonal, 'lastname',['placeholder' => Yii::t('site', 'Last name'), 'class' => $isPersonalSubmitted ? 'account-submitted' : '']); ?>
	        </div>
	    </div>
        <div class="row">
            <?php echo $form->error($accountPersonal       ,'professional_status_id'); ?>
            <div class="field">
                <?php echo $form->labelEx($accountPersonal     ,'professional_status_id'); ?>
                <?php echo $form->dropDownList($accountPersonal,'professional_status_id', $statuses); ?>
            </div>
        </div>
        <div class="row"></div>
        <div class="reg terms-confirm">
            <?= $form->error($userPersonal, 'agree_with_terms'); ?>
            <?= $form->checkBox($userPersonal, 'agree_with_terms', ['value' => 'yes', 'uncheckValue' => null]); ?>
            <?= $form->labelEx($userPersonal, 'agree_with_terms', ['label' => 'Я принимаю <a href="#" class="terms">Условия и Лицензионное соглашение</a>']); ?>
        </div>
	    <div class="row buttons">
            <div class="field">
	        <?php echo CHtml::submitButton(Yii::t('site', 'Start'), ['name' => 'personal']); ?>
            </div>
	    </div>
	    <?php $this->endWidget(); ?>
	</div>
	<!-- --------------------------------------------------------------------------------------------------------- -->
	<div class="form form-account-corporate">
	    <h1><?php echo Yii::t('site', 'Corporate account') ?></h1>
        <p class="ProximaNova-Bold-22px p-chose-accaount-type">(Вы - работодатель)</p>
	    <ul>
			<li><?php echo Yii::t('site', 'Package of simulations to assess others') ?></li>
			<li><?php echo Yii::t('site', 'Simple but powerful tool for assessment process') ?></li>
			<li><?php echo Yii::t('site', 'Comprehensive statistics on people and skills') ?>*</li>
		</ul>
	    <?php $form = $this->beginWidget('CActiveForm', array(
	        'id'                   => 'user-account-corporate-form',
	        'enableAjaxValidation' => false,
	    )); ?>
        <div class="row wide">
                <?php echo $form->error($profileCorporate, 'email'); ?>
            <div class="field">
                <?php echo $form->labelEx($profileCorporate, 'Email'); ?>
                <?php echo $form->textField($profileCorporate, 'email', ['placeholder' => $profileCorporate->getAttributeLabel('email'), 'class' => $isCorporateSubmitted ? 'account-submitted' : '']); ?>
            </div>
        </div>
	    <div class="row">
                <?php echo $form->error($userCorporate, 'password'); ?>
                <?php echo $form->error($userCorporate, 'password_again', ['class' => 'errorMessage right']); ?>
            <div class="field">
                <?php echo $form->labelEx($userCorporate  , Yii::t('site', 'Password')); ?>
                <?php echo $form->passwordField($userCorporate, 'password', ['placeholder' => Yii::t('site', 'Password'), 'class' => $isCorporateSubmitted ? 'account-submitted' : '']); ?>
                <?php echo $form->passwordField($userCorporate, 'password_again', ['placeholder' => $userCorporate->getAttributeLabel('password_again'), 'class' => $isCorporateSubmitted ? 'account-submitted' : '']); ?>
            </div>
                <?php echo $form->error($profileCorporate    , 'firstname'); ?>
                <?php echo $form->error($profileCorporate    , 'lastname', ['class' => 'errorMessage right']); ?>
            <div class="field">
            <?php echo $form->labelEx($profileCorporate  , 'Name'); ?>
            <?php echo $form->textField($profileCorporate, 'firstname',['placeholder' => Yii::t('site', 'First name'), 'class' => $isCorporateSubmitted ? 'account-submitted' : '']); ?>
            <?php echo $form->textField($profileCorporate, 'lastname',['placeholder' => Yii::t('site', 'Last name'), 'class' => $isCorporateSubmitted ? 'account-submitted' : '']); ?>
            </div>

	    </div>
	    <div class="row">
            <?php if ($isCorporateSubmitted): ?>
                <?php echo $form->error($accountCorporate, 'industry_id'); ?>
            <?php endif ?>
            <div class="field">
                <?php echo $form->labelEx($accountCorporate     , 'industry_id'); ?>
    	        <?php echo $form->dropDownList($accountCorporate, 'industry_id', $industries); ?>
            </div>
	    </div>
        <div class="row"></div>
        <div class="reg terms-confirm">
            <?= $form->error($userCorporate, 'agree_with_terms'); ?>
            <?= $form->checkBox($userCorporate, 'agree_with_terms', ['value' => 'yes', 'uncheckValue' => null]); ?>
            <?= $form->labelEx($userCorporate, 'agree_with_terms', ['label' => 'Я принимаю <a href="#" class="terms">Условия и Лицензионное соглашение</a>']); ?>
        </div>
	    <div class="row buttons">
            <div class="field">
	        <?php echo CHtml::submitButton(Yii::t('site', 'Start'), ['name' => 'corporate']); ?>
            </div>
	    </div>
	    <?php $this->endWidget(); ?>
	</div>
	<!-- --------------------------------------------------------------------------------------------------------- -->
	<div style="clear:both;"></div>
	<p class="note" style="float: none; clear: both;">
	    * <?php echo Yii::t('site', 'Не доступно в текущей версии, будет добавлено в следующем релизе') ?>
	</p>
</section>
