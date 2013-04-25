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

<div class="enter_form">
    <p>
        <?php echo Yum::t('Пожалуйста, заполните следующую форму'); ?>
    </p>

    <?php echo CHtml::beginForm(array('//user/auth/login'));  ?>
    <?php if(isset($_GET['action'])) echo CHtml::hiddenField('returnUrl', urldecode($_GET['action']));?>

    <div class="nice-border">
        <div class="row">
            <!--
            <?php
            if($module->loginType & UserModule::LOGIN_BY_USERNAME
                    || $module->loginType & UserModule::LOGIN_BY_LDAP)
            echo CHtml::activeLabelEx($model,'username');
            if($module->loginType & UserModule::LOGIN_BY_EMAIL)
                printf ('<label for="YumUserLogin_username">%s <span class="required">*</span></label>', Yum::t('Email'));
            if($module->loginType & UserModule::LOGIN_BY_OPENID)
                printf ('<label for="YumUserLogin_username">%s <span class="required">*</span></label>', Yum::t('OpenID username'));  ?>
            -->
            <?php echo CHtml::activeTextField($model,'username',['placeholder' => Yii::t('site', 'Enter your email address')]) ?>
            <?php echo CHtml::error($model,'username') ?>
        </div>
        <div class="row">
            <!--
            <?php echo CHtml::activeLabelEx($model,'Пароль'); ?>
            -->
            <?php echo CHtml::activePasswordField($model,'password',['placeholder' => Yii::t('site', 'Password')]); ?>
            <?php echo CHtml::error($model,'password') ?>
            <?php
            if($module->loginType & UserModule::LOGIN_BY_OPENID):
                echo '<br />'. Yum::t('When logging in with OpenID, password can be omitted');
            endif;
            ?>
        </div>
        <div class="row submit">
            <?php echo CHtml::submitButton(Yum::t('Войти')); ?>
        </div>
    </div>
    <div class="row rememberMe">
        <?php echo CHtml::activeCheckBox($model,'rememberMe', array('style' => 'display: inline;', 'class' => 'niceCheck')); ?>
        <?php echo CHtml::activeLabelEx($model,'rememberMe', array('style' => 'display: inline;')); ?>
    </div>
    <?php echo CHtml::endForm(); ?>
</div>

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
        var errors = $(".enter_form .errorMessage");
        var submit = $('.action-controller-login-auth #usercontent input[type="submit"]');
        for (var i=0; i < errors.length;i++) {
            var inp = $(errors[i]).prev("input");
            $(inp).css({"border":"2px solid #bd2929"});
            $(errors[i]).addClass($(inp).attr("id"));
            $(submit).height(48);
        }
    });
</script>