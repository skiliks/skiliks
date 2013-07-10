<div class="form invite-people-form sideform darkblueplacehld">

    <h2>Отправить приглашение</h2>

    <div  class="block-border bg-rich-blue border-large pull-left">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'invite-form'
        )); ?>

        <span class="form-global-errors">
            <?php echo $form->error($invite, 'invitations'); // You has no available invites! ?>
        </span>

        <div class="row <?php echo ($form->error($invite, 'firstname') || $form->error($invite, 'lastname')) ? 'error' : ''; ?>">
            <?php echo $form->labelEx($invite, 'full_name'); ?>
            <?php echo $form->textField($invite, 'firstname', ['placeholder' => Yii::t('site','First name')]); ?>
            <?php echo $form->error($invite, 'firstname'); ?>
            <?php echo $form->textField($invite, 'lastname', ['placeholder'  => Yii::t('site','Last Name')]); ?>
            <?php echo $form->error($invite, 'lastname'); ?>
        </div>

        <div class="row <?php echo ($form->error($invite, 'email')) ? 'error' : ''; ?>">
            <?php echo $form->labelEx($invite, 'email'); ?>
            <?php echo $form->textField($invite, 'email', ['placeholder' => Yii::t('site','Enter Email address')]); ?>
            <?php echo $form->error($invite, 'email'); ?>
        </div>

        <div class="row wide <?php echo (0 == count($vacancies) ? 'no-border' : '') ?> <?php echo ($form->error($invite, 'vacancy_id')) ? 'error' : ''; ?>v">
            <?php echo $form->labelEx($invite, 'vacancy_id'); ?>
            <?php echo $form->dropDownList($invite, 'vacancy_id', $vacancies); ?>
            <?php echo $form->error($invite, 'vacancy_id'); ?>
        </div>

        <?php $this->endWidget(); ?>
        </div>
</div>

<div class="form-simple form-large">
    <div class="block-border bg-transparnt rows-inline">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'account-corporate-password-form'
        )); ?>

        <div class="row">
            <?php echo $form->labelEx($passwordForm, 'currentPassword'); ?>
            <?php echo $form->passwordField($passwordForm, 'currentPassword'); ?>
            <?php echo $form->error($passwordForm, 'currentPassword'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($passwordForm, 'password'); ?>
            <?php echo $form->passwordField($passwordForm, 'password'); ?>
            <?php echo $form->error($passwordForm, 'password'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($passwordForm, 'verifyPassword'); ?>
            <?php echo $form->passwordField($passwordForm, 'verifyPassword'); ?>
            <?php echo $form->error($passwordForm, 'verifyPassword'); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>

<div class="form-simple form-large" style="margin-top: 20px;">
    <div class="block-border bg-transparnt rows-inline">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'account-corporate-password-form'
        )); ?>

        <div class="row">
            <?php echo $form->labelEx($passwordForm2, 'currentPassword'); ?>
            <?php echo $form->passwordField($passwordForm2, 'currentPassword'); ?>
            <?php echo $form->error($passwordForm2, 'currentPassword'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($passwordForm2, 'password'); ?>
            <?php echo $form->passwordField($passwordForm2, 'password'); ?>
            <?php echo $form->error($passwordForm2, 'password'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($passwordForm2, 'verifyPassword'); ?>
            <?php echo $form->passwordField($passwordForm2, 'verifyPassword'); ?>
            <?php echo $form->error($passwordForm2, 'verifyPassword'); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>

<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>