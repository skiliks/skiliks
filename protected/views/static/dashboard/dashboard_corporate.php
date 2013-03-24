
<section class="dashboard">
    <h1><?php echo Yii::t('site', 'Dashboard') ?></h1>

    <br/>

    <!-- invite-people-box -->
    <div id="invite-people-box" class="nice-border backgroud-rich-blue">
        <?php $this->renderPartial('_invite_people_box', [
            'invite'    => $invite,
            'positions' => $positions,
        ]) ?>
    </div>

    <!-- corporate-invitations-list-box -->
    <div id="corporate-invitations-list-box" class="nice-border backgroud-light-yellow">
        <?php $this->renderPartial('_corporate_invitations_list_box', [
            'inviteToEdit'    => $inviteToEdit,
            'positions' => $positions,
        ]) ?>
    </div>

    <!-- simulations-counter-box -->
    <div id="simulations-counter-box" class="nice-border backgroud-light-blue">
        <?php $this->renderPartial('_simulations_counter_box', []) ?>
    </div>
</section>


<?php if (!empty($valid)): ?>
    <div class="form form-invite-message message_window" title="Введите текст письма">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'send-invite-message-form',
        )); ?>

        <?php echo $form->hiddenField($invite, 'firstname'); ?>
        <?php echo $form->hiddenField($invite, 'lastname'); ?>
        <?php echo $form->hiddenField($invite, 'email'); ?>
        <?php echo $form->hiddenField($invite, 'position_id'); ?>

        <div class="row">
            <?php echo $form->labelEx($invite, 'To'); ?>
            <?php echo $invite->email ?> <br/><br/>
            <label></label>
            <?php echo $form->textField($invite, 'fullname'); ?>
        </div>

        <br/>
        <br/>

        <div class="row">
            <?php echo $form->labelEx($invite, 'message text'); ?>
            <?php echo $form->textArea($invite, 'message', ['rows' => 10, 'cols' => 60]); ?>
            <?php echo $form->error($invite, 'message'); ?>
        </div>

        <br/>
        <br/>

        <div class="row">
            <?php echo $form->labelEx($invite, 'signature'); ?>
            <?php echo $form->textField($invite, 'signature'); ?>
            <?php echo $form->error($invite, 'signature'); ?>
        </div>

        <br/>
        <br/>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Отправить', ['name' => 'send']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
<?php endif; ?>

<script type="text/javascript">
    $(function() {
        // @link: http://jqueryui.com/dialog/
        $( ".message_window" ).dialog({
            modal: true,
            width: 820
        });

        $( ".message_window").parent().addClass('nice-border');
        $( ".message_window").dialog('open');
    });
</script>