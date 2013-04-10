<section class="registration">
	<h2><?php echo Yii::t('site', 'Sign-up using your preferred option and get the results') ?></h2>
	<div class="form form-account-personal">
	    <h1><?php echo Yii::t('site', 'Personal account') ?></h1>
	    <ul>
			<li><?php echo Yii::t('site', 'Free full assessment of skills') ?></li>
			<li><?php echo Yii::t('site', 'Skills comparison with others') ?>*</li>
			<li><?php echo Yii::t('site', 'Updates for free') ?></li>
		</ul>
	    <?php $form = $this->beginWidget('CActiveForm', array(
	        'id'                   => 'user-account-personal-form',
	        'enableAjaxValidation' => false,
	    )); ?>
	    <div class="row">
	        <?php echo $form->hiddenField($accountPersonal, 'user_id'); ?>
	        <?php echo $form->error($accountPersonal      , 'user_id'); ?>
	        <?php echo $form->hiddenField($profile, 'email'); ?>
	        <?php echo $form->error($profile      , 'email'); ?>
	    </div>
	    <div class="row">
	        <?php echo $form->labelEx($profile  , 'Name'); ?>
	        <?php echo $form->textField($profile, 'firstname',['placeholder' => Yii::t('site', 'First name')]); ?>
	        <?php if ($isPersonalSubmitted): ?>
	            <?php echo $form->error($profile    , 'firstname'); ?>
	        <?php endif; ?>
	        <?php echo $form->textField($profile, 'lastname',['placeholder' => Yii::t('site', 'Last name')]); ?>
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
	        <?php echo $form->labelEx($accountPersonal     ,'professional_status_id'); ?>
	        <?php echo $form->dropDownList($accountPersonal,'professional_status_id', $statuses); ?>
	        <?php echo $form->error($accountPersonal       ,'professional_status_id'); ?>
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
			<li><?php echo Yii::t('site', 'Package of simulations to assess others') ?></li>
			<li><?php echo Yii::t('site', 'Simple but powerful tool for assessment process') ?></li>
			<li><?php echo Yii::t('site', 'Comprehensive statistics on people and skills') ?>*</li>
			<li><?php echo Yii::t('site', 'Updates for free') ?></li>
		</ul>
	    <?php $form = $this->beginWidget('CActiveForm', array(
	        'id'                   => 'user-account-corporate-form',
	        'enableAjaxValidation' => false,
	    )); ?>
	    <div class="row">
	        <?php echo $form->hiddenField($accountCorporate,'user_id'); ?>
	        <?php echo $form->error($accountCorporate,'user_id'); ?>
	        <?php echo $form->hiddenField($profile,'email'); ?>
	        <?php echo $form->error($profile      , 'email'); ?>
	    </div>
	    <div class="row">
	        <?php echo $form->labelEx($profile  , 'Name'); ?>
	        <?php echo $form->textField($profile, 'firstname',['placeholder' => Yii::t('site', 'First name')]); ?>
	        <?php if ($isCorporateSubmitted): ?>
	            <?php echo $form->error($profile    , 'firstname'); ?>
	        <?php endif; ?>
	        <?php echo $form->textField($profile, 'lastname',['placeholder' => Yii::t('site', 'Last name')]); ?>
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
                <?php echo $form->textField($accountCorporate, 'corporate_email',['placeholder' => 'Email@']); ?>
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
	    * <?php echo Yii::t('site', 'Currently not available and will be added in the next release') ?>
	</p>
</section>

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
