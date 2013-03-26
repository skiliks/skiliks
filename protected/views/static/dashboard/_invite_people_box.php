<div class="form invite-people-form">

    <h2>Пригласить соискателя</h2>

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'invite-form'
    )); ?>

    <?php echo $form->error($invite, 'invitations'); // You has no available invites! ?>

    <div class="row">
        <?php echo $form->labelEx($invite, 'full_name'); ?>
        <?php echo $form->textField($invite, 'firstname', ['placeholder' => Yii::t('site','First name')]); ?>
        <?php echo $form->textField($invite, 'lastname', ['placeholder'  => Yii::t('site','Last Name')]); ?>
        <?php echo $form->error($invite, 'firstname'); ?>
        <?php echo $form->error($invite, 'lastname'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($invite, 'email'); ?>
        <?php echo $form->textField($invite, 'email', ['placeholder' => Yii::t('site','Enter Email address')]); ?>
        <?php echo $form->error($invite, 'email'); ?>
    </div>

    <div class="row wide">
        <?php echo $form->labelEx($invite, 'vacancy_id'); ?>
        <?php echo $form->dropDownList($invite, 'vacancy_id', $vacancies); ?>
        <?php echo $form->error($invite, 'vacancy_id'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Отправить приглашение', ['name' => 'prevalidate']); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>