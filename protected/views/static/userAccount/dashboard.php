<style>
    .dashboard .form label {
        color: #555545;
        display: inline-block;
        font: 0.834em/1 "ProximaNova-Bold";
        margin-right: -5px;
        vertical-align: middle;
        width: 60px;
    }

    .dashboard .sbHolder {
        width: 334px;
    }
</style>

<section class="dashboard">
    <h2>Dashboard</h2>

    <div class="form">
        <h3>Invite People</h3>

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'invite-form'
        )); ?>

        <div class="row">
            <?php echo $form->labelEx($invite, 'Name'); ?>
            <?php echo $form->textField($invite, 'firstname', ['placeholder' => 'First name']); ?>
            <?php echo $form->textField($invite, 'lastname', ['placeholder' => 'Last Name']); ?>
            <?php echo $form->error($invite, 'firstname'); ?>
            <?php echo $form->error($invite, 'lastname'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($invite, 'email'); ?>
            <?php echo $form->textField($invite, 'email', ['placeholder' => 'Enter Email address']); ?>
            <?php echo $form->error($invite, 'email'); ?>
        </div>

        <div class="row wide">
            <?php echo $form->labelEx($invite, 'position_id'); ?>
            <?php echo $form->dropDownList($invite, 'position_id', $positions); ?>
            <?php echo $form->error($invite, 'position_id'); ?>
        </div>

        <div class="row wide">
            <label>Letter</label>
            <select>
                <option>WTF???</option>
            </select>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Send invite', ['name' => 'prevalidate']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>

    <?php if (!empty($valid)): ?>
    <div class="form">
        <h3>Message</h3>

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'send-invite-message-form'
        )); ?>

        <?php echo $form->hiddenField($invite, 'firstname'); ?>
        <?php echo $form->hiddenField($invite, 'lastname'); ?>
        <?php echo $form->hiddenField($invite, 'email'); ?>
        <?php echo $form->hiddenField($invite, 'position_id'); ?>

        <div class="row">
            <?php echo $form->labelEx($invite, 'To'); ?>
            <?php echo $form->textField($invite, 'fullname'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($invite, 'message'); ?>
            <?php echo $form->textArea($invite, 'message'); ?>
            <?php echo $form->error($invite, 'message'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($invite, 'signature'); ?>
            <?php echo $form->textField($invite, 'signature'); ?>
            <?php echo $form->error($invite, 'signature'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton('Send', ['name' => 'send']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
    <?php endif; ?>

    <?php
    $dataProvider = new CActiveDataProvider('Invite', [
        'criteria' => [
            'condition' => 'inviting_user_id = :myId',
            'order' => 'id DESC',
            'params' => ['myId' => $user->id]
        ],
        'pagination' => [
            'pageSize' => 20
        ]
    ]);

    $this->widget('zii.widgets.grid.CGridView', array(
        'dataProvider' => $dataProvider,
        'columns' => ['invited_user_id', 'position_id', 'status', 'sent_time']
    ));

    ?>

</section>