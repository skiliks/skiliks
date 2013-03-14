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
	
	    <?php echo $form->errorSummary($accountPersonal); ?>
	
	    <div class="row">
	        <?php echo $form->hiddenField($accountPersonal,'user_id'); ?>
	        <?php echo $form->error($accountPersonal    ,'user_id'); ?>
	    </div>
	
	    <div class="row">
	        <?php echo $form->labelEx($accountPersonal     ,'industry_id'); ?>
	        <?php echo $form->dropDownList($accountPersonal,'industry_id', $industries); ?>
	        <?php echo $form->error($accountPersonal       ,'industry_id'); ?>
	    </div>
	
	    <div class="row">
	        <?php echo $form->labelEx($accountPersonal     ,'position_id'); ?>
	        <?php echo $form->dropDownList($accountPersonal,'position_id', $positions); ?>
	        <?php echo $form->error($accountPersonal       ,'position_id'); ?>
	    </div>
	
	    <div class="row buttons">
	        <?php echo CHtml::submitButton(Yii::t('site', 'Submit')); ?>
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
	
	    <?php echo $form->errorSummary($accountCorporate); ?>
	
	    <div class="row">
	        <?php echo $form->hiddenField($accountCorporate,'user_id'); ?>
	        <?php echo $form->error($accountCorporate,'user_id'); ?>
	    </div>
	
	    <div class="row">
	        <?php echo $form->labelEx($accountPersonal,'industry_id'); ?>
	        <?php echo $form->dropDownList($accountCorporate,'industry_id', $industries); ?>
	        <?php echo $form->error($accountCorporate,'industry_id'); ?>
	    </div>
	
	    <div class="row buttons">
	        <?php echo CHtml::submitButton(Yii::t('site', 'Submit')); ?>
	    </div>
	
	    <?php $this->endWidget(); ?>
	
	</div>
	
	<!-- --------------------------------------------------------------------------------------------------------- -->
	
	<p class="note"><?php echo Yii::t('site', 'Fields with * are required.') ?></p>
</section>