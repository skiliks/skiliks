<h2> <?php echo Yii::t('site', 'Your account has been activated'); ?> </h2>

<p> <?php Yum::t('Click {here} to go to the login form', array(
			'{here}' => CHtml::link(Yum::t('here'), Yum::module()->loginUrl
				))); ?> </p>
