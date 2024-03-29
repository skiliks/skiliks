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
        <p class="p-chose-accaount-type">(Вы - сотрудник или соискатель)</p>
        <ul>
			<li><?php echo Yii::t('site', 'Возможность получать приглашения от работодателя') ?></li>
			<li><?php echo Yii::t('site', 'Полная версия по приглашению') ?></li>
			<li><?php echo Yii::t('site', 'Демо-версия бесплатно') ?></li>
		</ul>
	    <?php $form = $this->beginWidget('CActiveForm', array(
	        'id'                   => 'user-account-personal-form',
	        'enableAjaxValidation' => false,
	    )); ?>
	    <div class="row hidden">
	        <?php echo $form->hiddenField($accountPersonal, 'user_id'); ?>
	        <?php echo $form->error($accountPersonal      , 'user_id'); ?>
	        <?php echo $form->hiddenField($profile, 'email'); ?>
	        <?php echo $form->error($profile      , 'email'); ?>
	    </div>
	    <div class="row">
            <?php if ($isPersonalSubmitted): ?>
                <?php echo $form->error($profile    , 'firstname'); ?>
                <?php echo $form->error($profile    , 'lastname', ['class' => 'errorMessage right']); ?>
            <?php endif; ?>
            <div class="field">
	        <?php echo $form->labelEx($profile  , 'Name'); ?>
	        <?php echo $form->textField($profile, 'firstname',['placeholder' => Yii::t('site', 'First name'), 'class' => $isPersonalSubmitted ? 'account-submitted' : '']); ?>
	        <?php echo $form->textField($profile, 'lastname',['placeholder' => Yii::t('site', 'Last name'), 'class' => $isPersonalSubmitted ? 'account-submitted' : '']); ?>
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
        <p class="p-chose-accaount-type">(Вы - работодатель)</p>
	    <ul>
			<li><?php echo Yii::t('site', '10 симуляций бесплатно (Полная версия)') ?></li>
			<li><?php echo Yii::t('site', 'Package of simulations to assess others') ?></li>
			<li><?php echo Yii::t('site', 'Simple but powerful tool for assessment process') ?></li>
		</ul>
	    <?php $form = $this->beginWidget('CActiveForm', array(
	        'id'                   => 'user-account-corporate-form',
	        'enableAjaxValidation' => false,
	    )); ?>
	    <div class="row hidden">
	        <?php echo $form->hiddenField($accountCorporate,'user_id'); ?>
	        <?php echo $form->error($accountCorporate,'user_id'); ?>
	        <?php echo $form->hiddenField($profile,'email'); ?>
	        <?php echo $form->error($profile      , 'email'); ?>
	    </div>
	    <div class="row">
            <?php if ($isCorporateSubmitted): ?>
                <?php echo $form->error($profile    , 'firstname'); ?>
                <?php echo $form->error($profile    , 'lastname', ['class' => 'errorMessage right']); ?>
            <?php endif; ?>
            <div class="field">
            <?php echo $form->labelEx($profile  , 'Name'); ?>
            <?php echo $form->textField($profile, 'firstname',['placeholder' => Yii::t('site', 'First name'), 'class' => $isCorporateSubmitted ? 'account-submitted' : '']); ?>
            <?php echo $form->textField($profile, 'lastname',['placeholder' => Yii::t('site', 'Last name'), 'class' => $isCorporateSubmitted ? 'account-submitted' : '']); ?>
            </div>

	    </div>


	    <div class="row">
            <?php echo $form->error($accountCorporate       , 'industry_id'); ?>
            <div class="field">
                <?php echo $form->labelEx($accountCorporate     , 'industry_id'); ?>
    	        <?php echo $form->dropDownList($accountCorporate, 'industry_id', $industries); ?>
            </div>
	    </div>
	    <div class="row wide">
            <?php if (UserService::isCorporateEmail($profile->email)): ?>
                <?php echo $form->hiddenField($accountCorporate, 'corporate_email'); ?>
            <?php else: ?>
                <?php echo $form->error($accountCorporate    , 'corporate_email'); ?>
                <div class="field">
                <?php echo $form->labelEx($accountCorporate  , 'corporate_email'); ?>
                <?php echo $form->textField($accountCorporate, 'corporate_email',['placeholder' => 'Email@', 'class' => $isCorporateSubmitted ? 'account-submitted' : '']); ?>
                </div>
            <?php endif ?>
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
</section>
