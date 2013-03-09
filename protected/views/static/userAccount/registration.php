
<br/>
<br/>
<br/>
<br/>

<div class="form">

<?php $form = $this->beginWidget('CActiveForm', array(
	'id'                   => 'yum-user-registration-form',
	'enableAjaxValidation' => false,
)); ?>

	<?php // echo $form->errorSummary($user); ?>
	<?php // echo $form->errorSummary($profile); ?>

    <br/>
    <div class="row">
        <?php echo $form->labelEx($profile  , 'email'); ?>
        <?php echo $form->textField($profile, 'email'); ?>
        <?php echo $form->error($profile    , 'email'); ?>
    </div>

    <br/>

    <div class="row">
		<?php echo $form->labelEx($user      , 'password'); ?>
		<?php echo $form->passwordField($user, 'password'); ?>
		<?php echo $form->error($user        , 'password'); ?>
	</div>

    <br/>

    <div class="row">
        <?php echo $form->labelEx($user      , 'password_again'); ?>
        <?php echo $form->passwordField($user, 'password_again'); ?>
        <?php echo $form->error($user        , 'password_again'); ?>
    </div>

    <br/>
        <a id="pass-switcher"><?php echo Yii::t('site', 'Show passwords') ?></a>
    <br/>
    <br/>

    <p class="note"><?php echo Yii::t('site', 'Fields with * are required.') ?></p>

    <br/>

    <div class="row buttons">
        <?php echo CHtml::submitButton(Yii::t('site', 'Submit')); ?>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">
    $('#pass-switcher').click(function(event){
        // show * or password letters switcher {
        event.preventDefault();

        var passInput = $('#YumUser_password');
        var passAgainInput = $('#YumUser_password_again');

        if ('password' == passInput.attr('type')) {
            passInput.clone().attr('type','text').insertAfter(passInput).prev().remove();
        } else {
            passInput.clone().attr('type','password').insertAfter(passInput).prev().remove();
        }

        if ('password' == passAgainInput.attr('type')) {
            passAgainInput.clone().attr('type','text').insertAfter(passAgainInput).prev().remove();
        } else {
            passAgainInput.clone().attr('type','password').insertAfter(passAgainInput).prev().remove();
        }
        // show * or password letters switcher }
    });
</script>