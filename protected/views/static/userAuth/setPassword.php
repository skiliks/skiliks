
<section class="column-full pull-content-center">

    <br/>
    <br/>
    <h1>Сменить пароль</h1>
    <br/>
    <br/>

    <div class="column-2-3-fixed pull-center us-user-auth-page-form">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'change-password-form'
        )); ?>

        <div class="nice-border border-radius-standard background-sky">

            <br/>
            <br/>
            <br/>

            <div class="nice-border-not-transparent border-radius-standard
                pull-center background-D1E8EA us-auth-box <?= $passwordForm->hasErrors() ? 'error' : '' ?>">



                <span class="error-place pull-left">
                    <span class="us-first-input pull-content-left">
                        <?php echo $form->error($passwordForm, 'password') ?>
                    </span>
                    <span class="us-second-input pull-content-left">
                        <?php echo $form->error($passwordForm, 'verifyPassword') ?>
                    </span>
                </span>

                <?php echo $form->passwordField($passwordForm,'password',[
                    'placeholder' => Yii::t('site', 'Enter new password'),
                    'class'       => 'inputs-wide-height',
                ]) ?>


                <?php echo $form->passwordField($passwordForm, 'verifyPassword', [
                    'placeholder' => Yii::t('site', 'Confirm password'),
                    'class'       => 'inputs-wide-height margin-left-8',
                ]); ?>


                <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), [
                    'class' => 'background-dark-blue icon-circle-with-blue-arrow-big
                        button-standard us-button-submit icon-padding-standard margin-left-8'
                ]); ?>
            </div>

            <?php $this->endWidget(); ?>
            <br/>
            <br/>
            <br/>
        </div>

    </div>
</section>


<div class="clearfix"></div>