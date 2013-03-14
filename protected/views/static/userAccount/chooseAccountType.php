<style>
    .form label {
        display: inline-block;
        width: 160px;
    }
    .form .errorMessage {
        background-color: #f5dfb4;
        border: 1px solid #cd0a0a;
        border-radius: 7px;
        color: #cd0a0a;
        margin: 5px;
        opacity: 0.7;
        padding: 7px;
        width: 340px;
    }
</style>


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
	        <?php echo $form->labelEx($accountCorporate  , 'corporate_email'); ?>
	        <?php echo $form->textField($accountCorporate, 'corporate_email'); ?>
	        <?php echo $form->error($accountCorporate    , 'corporate_email'); ?>
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
