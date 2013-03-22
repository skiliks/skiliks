<h2> <?php echo Yii::t('site','Activation did not work'); ?> </h2>

<?php if($error == -1) echo Yii::t('site','The user is already activated'); ?>
<?php if($error == -2) echo Yii::t('site','Wrong activation Key'); ?>
<?php if($error == -3) echo Yii::t('site','Profile found, but no associated user. Possible database inconsistency. Please contact the System administrator with this error message, thank you'); ?>
