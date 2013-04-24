<?php
if(!isset($model)) 
	$model = new YumUserLogin();

$module = Yum::module();

$this->pageTitle = Yum::t('Login');
if(isset($this->title))
$this->title = Yum::t('Login');
$this->breadcrumbs=array(Yum::t('Login'));

Yum::renderFlash();
?>

<div class="form">
<p>
<?php
echo Yum::t(
		'Пожалуйста, заполните следующую форму с ваших учетных данных:'); ?>
</p>

<?php echo CHtml::beginForm(array('//user/auth/login'));  ?>

<?php
if(isset($_GET['action']))
	echo CHtml::hiddenField('returnUrl', urldecode($_GET['action']));
?>

	<div class="row">
		<?php
		if($module->loginType & UserModule::LOGIN_BY_USERNAME 
				|| $module->loginType & UserModule::LOGIN_BY_LDAP)
		echo CHtml::activeLabelEx($model,'username'); 
		if($module->loginType & UserModule::LOGIN_BY_EMAIL)
			printf ('<label for="YumUserLogin_username">%s <span class="required">*</span></label>', Yum::t('Email'));
		if($module->loginType & UserModule::LOGIN_BY_OPENID)
			printf ('<label for="YumUserLogin_username">%s <span class="required">*</span></label>', Yum::t('OpenID username'));  ?>

		<?php echo CHtml::activeTextField($model,'username') ?>
		<?php echo CHtml::error($model,'username') ?>
	</div>
	
	<div class="row">
		<?php echo CHtml::activeLabelEx($model,'Пароль'); ?>
		<?php echo CHtml::activePasswordField($model,'password'); ?>
        <?php echo CHtml::error($model,'password') ?>
		<?php
        if($module->loginType & UserModule::LOGIN_BY_OPENID):
			echo '<br />'. Yum::t('When logging in with OpenID, password can be omitted');
        endif;
        ?>
		
	</div>
	
	<div class="row">
	<p class="hint">
	<?php /*
	if(Yum::hasModule('registration') && Yum::module('registration')->enableRegistration)
	echo CHtml::link(Yum::t("Registration"),
			Yum::module('registration')->registrationUrl);
	if(Yum::hasModule('registration') 
			&& Yum::module('registration')->enableRegistration
			&& Yum::module('registration')->enableRecovery)
	echo ' | ';
	if(Yum::hasModule('registration') 
			&& Yum::module('registration')->enableRecovery) 
	echo CHtml::link(Yum::t("Lost password?"),
			Yum::module('registration')->recoveryUrl);
	*/ ?>
</p>
	</div>

<div class="row rememberMe">
<?php echo CHtml::activeCheckBox($model,'rememberMe', array('style' => 'display: inline;')); ?>
<?php echo CHtml::activeLabelEx($model,'rememberMe', array('style' => 'display: inline;')); ?>
</div>

<div class="row submit">
<?php echo CHtml::submitButton(Yum::t('Login')); ?>
</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->

<?php
$form = new CForm(array(
			'elements'=>array(
				'username'=>array(
					'type'=>'text',
					'maxlength'=>32,
					),
				'password'=>array(
					'type'=>'password',
					'maxlength'=>32,
					),
				'rememberMe'=>array(
					'type'=>'checkbox',
					)
				),

			'buttons'=>array(
				'login'=>array(
					'type'=>'submit',
					'label'=>'Login',
					),
				),
			), $model);
?>

<script>
    $(document).ready(function(){
        var errors = $(".errorMessage");
        for (var i=0; i < errors.length;i++) {
            var inp = $(errors[i]).prev("input");
            $(inp).css({"border":"2px solid #bd2929"});
            $(errors[i]).addClass($(inp).attr("id"));
        }
    });
</script>