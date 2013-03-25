<div class="form form-invite-message-editor dashboard" title="Текст приглашения">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'edit-invite-form',
    )); ?>

    <?php echo $form->hiddenField($invite, 'id'); ?>

    <div class="row">
        <?php echo $form->labelEx($invite  , 'To'); ?>
        <?php echo $form->textField($invite, 'firstname'); ?><?php echo $form->textField($invite, 'lastname'); ?>
        <br/><br/>
        <?php echo $form->error($invite    , 'firstname'); ?>
        <?php echo $form->error($invite    , 'lastname'); ?>
    </div>

    <br/>
    <br/>

    <div class="row">
        <?php echo $form->labelEx($invite     , 'vacancy_id'); ?>
        <?php echo $form->dropDownList($invite, 'vacancy_id', $vacancies); ?>
        <br/><br/>
        <?php echo $form->error($invite       , 'vacancy_id'); ?>
    </div>


    <div class="row buttons">
        <?php echo CHtml::submitButton('Сохранить правки', ['name' => 'edit-invite']); ?>
    </div>



    <?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
    $(function() {
        // @link: http://jqueryui.com/dialog/
         $( ".form-invite-message-editor").dialog({
            modal: true,
            width: 780

        });
        $( ".form-invite-message-editor").dialog('close');
        $( ".form-invite-message-editor").parent().addClass('nice-border');

        $('.edit-invite').click(function(event){
            event.preventDefault();

            // invite id, vacancy id, firtsname, lastname
            var data = $(this).attr('href').split('&&');
            var names = $(this).attr('title').split(', ');

            $('#edit-invite-form input#Invite_id').val(data[0]);
            $('#edit-invite-form input#Invite_firstname').val(names[0]);
            $('#edit-invite-form input#Invite_lastname').val(names[1]);
            $('#edit-invite-form select#Invite_vacancy_id [value=' + data[1] + ']').attr("selected", "selected");

            $('#edit-invite-form select#Invite_vacancy_id').selectbox('detach');
            $('#edit-invite-form select#Invite_vacancy_id').selectbox('attach');

            $( ".form-invite-message-editor").dialog('open');
        });
    });
</script>