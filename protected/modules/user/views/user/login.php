<?php
if(!isset($model)) {
    $model = new YumUserLogin();
}


$module = Yum::module();

//$this->pageTitle = Yum::t('Login');
//if(isset($this->title))
//$this->title = Yum::t('Login');
//$this->breadcrumbs=array(Yum::t('Login'));

?>
<section class="column-full pull-content-center">

    <br/>
    <br/>
    <h1>Вход</h1>
    <br/>
    <br/>

    <div class="column-2-3-fixed pull-center us-user-auth-page-form">

        <?php echo CHtml::beginForm(array('//user/auth/login'));  ?>

        <?php if(isset($_GET['action'])) echo CHtml::hiddenField('returnUrl', urldecode($_GET['action']));?>


        <div class="nice-border border-radius-standard background-sky">
            <br/>
            <h2 class="color-6D6D5B">
                <?php echo Yum::t('Пожалуйста, заполните следующую форму'); ?>
            </h2>
            <br/>

            <div class="nice-border-not-transparent border-radius-standard
                pull-center background-D1E8EA us-auth-box <?= $model->hasErrors() ? 'error' : '' ?>">
                <!--
                <?php /*
                if($module->loginType & UserModule::LOGIN_BY_USERNAME
                        || $module->loginType & UserModule::LOGIN_BY_LDAP)
                echo CHtml::activeLabelEx($model,'username');
                if($module->loginType & UserModule::LOGIN_BY_EMAIL)
                    printf ('<label for="YumUserLogin_username">%s <span class="required">*</span></label>', Yum::t('Email'));
                if($module->loginType & UserModule::LOGIN_BY_OPENID)
                    printf ('<label for="YumUserLogin_username">%s <span class="required">*</span></label>', Yum::t('OpenID username'));  ?>
                */ ?> -->

                <span class="error-place pull-left">
                    <span class="us-first-input pull-content-left">
                        <?php echo CHtml::error($model,'username') ?>
                    </span>
                    <span class="us-second-input pull-content-left">
                        <?php echo CHtml::error($model,'password') ?>
                    </span>
                </span>

                <?php echo CHtml::activeTextField($model,'username',[
                    'placeholder' => Yii::t('site', 'Enter your email address'),
                    'class'       => 'inputs-wide-height',
                ]) ?>

                <!--
                <?php // echo CHtml::activeLabelEx($model,'Пароль'); ?>
                -->
                <?php echo CHtml::activePasswordField($model, 'password', [
                    'placeholder' => Yii::t('site', 'Password'),
                    'class'       => 'inputs-wide-height margin-left-8',
                ]); ?>

                <?php /*if($module->loginType & UserModule::LOGIN_BY_OPENID):
                    echo '<br />'. Yum::t('When logging in with OpenID, password can be omitted');
                endif; */ ?>

                <?php echo CHtml::submitButton(Yum::t('Войти'), [
                    'class' => 'label background-dark-blue icon-circle-with-blue-arrow-big
                        button-standard icon-padding-standard margin-left-8'
                ]); ?>
            </div>

            <?php // for "your email not activated" message { ?>
            <div id="yum-login-global-errors">
                <?php echo CHtml::error($model,'form') ?>
                <script>
                    <?php // to prevent update text by Cufon - it`s brake link "send activation email again" ?>
                    $('#yum-login-global-errors .errorMessage').addClass('globalErrorMessage');
                    $('#yum-login-global-errors .errorMessage').removeClass('errorMessage');
                </script>
            </div>
            <?php // for "your email not activated" message } ?>

            <div class="row">
                <?php echo CHtml::activeCheckBox($model, 'rememberMe', [
                    'class' => 'inline-block'
                ]); ?>
                <?php echo CHtml::activeLabelEx($model, 'rememberMe', [
                    'class' => 'inline-block us-label color-146672'
                ]); ?>
            </div>
            <?php echo CHtml::endForm(); ?>

            <br/>

        </div>
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
</section>

<div class="clearfix"></div>