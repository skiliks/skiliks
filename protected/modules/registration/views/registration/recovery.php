<?php
$this->pageTitle = Yum::t('Восстановление пароля');

$this->breadcrumbs=array(
	Yum::t('Войти') => Yum::module()->loginUrl,
	Yum::t('Восстановить'));

?>
<?php if(Yum::hasFlash()) {
echo '<div class="success">';
echo Yum::getFlash(); 
echo '</div>';
} else {
echo '<h2>'.Yum::t('Восстановление пароля').'</h2>';
?>

<div class="form">
<?php echo CHtml::beginForm(); ?>

	<?php echo CHtml::errorSummary($form); ?>
	
	<div class="row">
		<?php echo CHtml::activeLabel($form,'login_or_email'); ?>
		<?php echo CHtml::activeTextField($form,'login_or_email') ?>
		<?php echo CHtml::error($form,'login_or_email'); ?>
		<p class="hint"><?php echo Yum::t("Пожалуйста, введите ваше имя пользователя или адрес электронной почты."); ?></p>
	</div>
	
	<div class="row submit">
		<?php echo CHtml::submitButton(Yum::t('Восстановить')); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->
<?php } ?>
