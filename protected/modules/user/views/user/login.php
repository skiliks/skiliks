<?php
if(!isset($model)) {
    $model = new YumUserLogin();
}

$module = Yum::module();
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

            <?php // var_dump($model->getErrors()) ?>

            <div class="nice-border-not-transparent border-radius-standard
                pull-center background-D1E8EA us-auth-box <?= $model->hasErrors() ? 'error' : '' ?>
                <?= ('' != $model->getError('form')) ? 'global-error' : '' ?>">

                <span class="error-place pull-left">
                    <span class="us-first-input pull-content-left">
                        <?php echo CHtml::error($model, 'username') ?>
                    </span>
                    <span class="us-second-input pull-content-left">
                        <?php echo CHtml::error($model,'password') ?>
                    </span>
                </span>

                <?php echo CHtml::activeTextField($model,'username',[
                    'placeholder' => Yii::t('site', 'Enter your email address'),
                    'class'       => 'inputs-wide-height',
                ]) ?>

                <?php echo CHtml::activePasswordField($model, 'password', [
                    'placeholder' => Yii::t('site', 'Password'),
                    'class'       => 'inputs-wide-height margin-left-8',
                ]); ?>

                <?php echo CHtml::submitButton(Yum::t('Войти'), [
                    'class' => 'label background-dark-blue icon-circle-with-blue-arrow-big
                        button-standard icon-padding-standard margin-left-8 us-button-submit'
                ]); ?>

                <?php // for "your email not activated" message { ?>
                <span class="<?= ('' != CHtml::error($model, 'form')) ? 'error' : 'hide' ?> pull-content-left us-error-bottom">
                <span class="error-place pull-left">
                    <span class="pull-content-left">
                        <?php echo CHtml::error($model, 'form') ?>
                    </span>
                </span>
            </span>
                <?php // for "your email not activated" message } ?>
            </div>

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

            <?php if ($model->getError('bruteforce')) : ?>
                <script type="text/javascript">
                    // обрабатівается в common.js
                    window.yourAccountBanned = true;
                </script>
            <?php endif; ?>

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
</section>

<div class="clearfix"></div>