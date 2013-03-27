<div class="form form-decline-explanation">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'     => 'form-decline-explanation',
        'action' => $action
    )); ?>

    <?php echo $form->hiddenField($declineExplanation, 'invite_id'); ?>
    <?php echo $form->error($declineExplanation, 'invite_id'); ?>

    <h2>
        Пожалуйста, укажите причину отказа
    </h2>

    <div class="row">
        <?php echo $form->labelEx($declineExplanation        , 'reason_id'); ?>
        <?php echo $form->RadioButtonList($declineExplanation, 'reason_id', $reasons); ?>
        <?php echo $form->error($declineExplanation          , 'reason_id'); ?>
    </div>

    <br/>
    <br/>

    <div class="row">
        <?php echo $form->labelEx($declineExplanation  , 'description'); ?>
        <?php echo $form->textArea($declineExplanation, 'description'); ?>
        <?php echo $form->error($declineExplanation    , 'description'); ?>
    </div>

    <br/>
    <br/>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Отказаться от прохождения симуляции', ['name' => 'decline', 'class' => 'confirm-decline']); ?>
        <?php echo CHtml::submitButton('Отменить', ['name' => 'return', 'class' => 'chancel-decline']); ?>
    </div>

    <br/>
    <br/>

    * Поля обязательные для заполнения

    <?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
    $('.chancel-decline').click(function(event){
        event.preventDefault();
        $('#invite-decline-form').dialog('close');
    });

    $('.confirm-decline').click(function(event){
        event.preventDefault();

        var data = $('#invite-decline-form form').serializeArray();

        $.ajax({
            url: '/dashboard/decline-invite/validation',
            data: data,
            type: 'POST',
            success: function(data) {
                if (true === data.isValid) {
                    $('#invite-decline-form').html(data.html);
                    $('#invite-decline-form form').submit();
                } else {
                    $('#invite-decline-form').html(data.html);
                }
            }
        });
    });
</script>