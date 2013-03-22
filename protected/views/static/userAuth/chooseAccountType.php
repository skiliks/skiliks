<section class="registration">
	<h2>Sign-up using your preferred option and get the results</h2>
	
	<div class="form form-account-personal">
	
	    <h1><?php echo Yii::t('site', 'Personal account') ?></h1>
	    
	    <ul>
			<li>Free full assesmnet of skills</li>
			<li>Skills comparison with others*</li>
			<li>Updates for free</li>
		</ul>
	
	    <?php $form = $this->beginWidget('CActiveForm', array(
	        'id'                   => 'user-account-personal-form',
	        'enableAjaxValidation' => false,
	    )); ?>
	
	    <?php // echo $form->errorSummary($accountPersonal); ?>
	
	    <div class="row">
	        <?php echo $form->hiddenField($accountPersonal, 'user_id'); ?>
	        <?php echo $form->error($accountPersonal      , 'user_id'); ?>
	
	        <?php echo $form->hiddenField($profile, 'email'); ?>
	        <?php echo $form->error($profile      , 'email'); ?>
	    </div>
	
	    <div class="row">
	        <?php echo $form->labelEx($profile  , 'Name'); ?>
	        <?php echo $form->textField($profile, 'firstname'); ?>
	        <?php if ($isPersonalSubmitted): ?>
	            <?php echo $form->error($profile    , 'firstname'); ?>
	        <?php endif; ?>
	        <?php echo $form->textField($profile, 'lastname'); ?>
	        <?php if ($isPersonalSubmitted): ?>
	            <?php echo $form->error($profile    , 'lastname'); ?>
	        <?php endif; ?>
	    </div>
	
	    <div class="row">
	        <?php echo $form->labelEx($accountPersonal     ,'industry_id'); ?>
	        <?php echo $form->dropDownList($accountPersonal,'industry_id', $industries); ?>
	        <?php echo $form->error($accountPersonal       ,'industry_id'); ?>
	    </div>
	
	    <div class="row wide">
	        <?php echo $form->labelEx($accountPersonal     ,'position_id'); ?>
	        <?php echo $form->dropDownList($accountPersonal,'position_id', $positions); ?>
	        <?php echo $form->error($accountPersonal       ,'position_id'); ?>
	    </div>
	
	    <div class="row buttons">
	        <?php echo CHtml::submitButton(Yii::t('site', 'Start and get the report'), ['name' => 'personal']); ?>
	    </div>
	
	    <?php $this->endWidget(); ?>
	
	</div>
	
	<!-- --------------------------------------------------------------------------------------------------------- -->
	
	<div class="form form-account-corporate">
	
	    <h1><?php echo Yii::t('site', 'Corporate account') ?></h1>
	    
	    <ul>
			<li>Package of simulations to assess others</li>
			<li>Simple but powerful tool for assessment process</li>
			<li>Comprehensive statistics on people and skills*</li>
			<li>Updates for free</li>
		</ul>
	
	    <?php $form = $this->beginWidget('CActiveForm', array(
	        'id'                   => 'user-account-corporate-form',
	        'enableAjaxValidation' => false,
	    )); ?>
	
	    <?php // echo $form->errorSummary($accountCorporate); ?>
	
	    <div class="row">
	        <?php echo $form->hiddenField($accountCorporate,'user_id'); ?>
	        <?php echo $form->error($accountCorporate,'user_id'); ?>
	
	        <?php echo $form->hiddenField($profile,'email'); ?>
	        <?php echo $form->error($profile      , 'email'); ?>
	    </div>
	
	    <div class="row">
	        <?php echo $form->labelEx($profile  , 'Name'); ?>
	        <?php echo $form->textField($profile, 'firstname'); ?>
	        <?php if ($isCorporateSubmitted): ?>
	            <?php echo $form->error($profile    , 'firstname'); ?>
	        <?php endif; ?>
	        <?php echo $form->textField($profile, 'lastname'); ?>
	        <?php if ($isCorporateSubmitted): ?>
	            <?php echo $form->error($profile    , 'lastname'); ?>
	        <?php endif; ?>
	    </div>
	
	    <div class="row">
	        <?php echo $form->labelEx($accountCorporate     , 'industry_id'); ?>
	        <?php echo $form->dropDownList($accountCorporate, 'industry_id', $industries); ?>
	        <?php echo $form->error($accountCorporate       , 'industry_id'); ?>
	    </div>
	
	    <div class="row wide">
            <?php if (UserService::isCorporateEmail($profile->email)): ?>
                <?php echo $form->hiddenField($accountCorporate, 'corporate_email'); ?>
            <?php else: ?>
                <?php echo $form->labelEx($accountCorporate  , 'corporate_email'); ?>
                <?php echo $form->textField($accountCorporate, 'corporate_email'); ?>
                <?php echo $form->error($accountCorporate    , 'corporate_email'); ?>
            <?php endif ?>
	    </div>
	
	    <div class="row buttons">
	        <?php echo CHtml::submitButton(Yii::t('site', 'Start and get the report'), ['name' => 'corporate']); ?>
	    </div>
	
	    <?php $this->endWidget(); ?>
	
	</div>
	
	<!-- --------------------------------------------------------------------------------------------------------- -->
	
	<div style="clear:both;"></div>
	
	<p class="note" style="float: none; clear: both;">
	    <?php echo Yii::t('site', '* Currently not available and will be added in the next release') ?>
	</p>
</section>

<!--div class="register-by-link-wrap">
	<div class="register-by-link">
		<h6 class="register-by-link-desc">Пожалуйста, зарегистрируйтесь, чтобы перейти к тестированию</h6>
		
		<div class="row">
			<label>Имя</label>
			<input type="text" placeholder="Имя" />
			<input type="text" placeholder="Фамилия" />
		</div>
		
		<div class="row">
			<label>Профессиональный статус</label>
			<select>
				<option value="Выберете статус">Выберете статус</option>
			</select>
		</div>
		
		<div class="row">
			<label>Отрасль</label>
			<select>
				<option value="Выберете статус">Выберете отрасль</option>
			</select>
		</div>
		
		<div class="row">
			<label>Пароль</label>
			<input type="password" placeholder="Пароль" />
		</div>
		
		<div class="row mb">
			<label>Подтверждение</label>
			<input type="password" placeholder="Подтверждение" />
		</div>
		
		<div class="row">
			<input type="submit" value="Зарегестрироваться" />
			<a href="#" class="cancel"><span>Отказаться от приглашения</span></a>
		</div>
	</div>
</div-->

<script type="text/javascript">
function setModalSize() {
	var h;
	if ($('body').height() > $('.container').height()) {
		h=$('body').height();
	}
	else {
		h=$('.container').height();
	}
	var w=$('body').width();
	var cw=$('.content').width()/2;
	var l=w/2-cw;
	
	$('.register-by-link-wrap').each(function(){
		$(this).css({
			'width':w,
			'height':h,
			'left':-l,
		})
	})
}
setModalSize();

$(window).resize(function(){
	setModalSize();
})
</script>
