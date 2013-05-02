<section class="dashboard corpdashboard">
    <h2 class="thetitle bigtitle"><?php echo Yii::t('site', 'Work dashboard') ?></h2>
    <aside>
    <!-- invite-people-box -->
        <div id="invite-people-box" class="nice-border backgroud-rich-blue sideblock">
            <?php $this->renderPartial('_invite_people_box', [
                'invite'    => $invite,
                'vacancies' => $vacancies,
            ]) ?>
        </div>

<?php if (true === $validPrevalidate): ?>
    <div class="form form-invite-message message_window" title="Сообщение">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'send-invite-message-form',
        )); ?>

        <?php echo $form->hiddenField($invite, 'firstname'); ?>
        <?php echo $form->hiddenField($invite, 'lastname'); ?>
        <?php echo $form->hiddenField($invite, 'email'); ?>
        <?php echo $form->hiddenField($invite, 'vacancy_id'); ?>

        <div class="row">
            <?php echo $form->labelEx($invite, 'To'); ?>
            <?php /* echo $invite->email */ ?>
            <label></label>
            <?php echo $form->textField($invite, 'fullname'); ?>
        </div>
        <div class="row">
            <p>
                <?= $invite->ownerUser->account_corporate->company_name ?: 'Компания' ?> предлагает вам пройти тест «Базовый менеджмент» для участия в конкурсе на вакансию <a href="<?= $invite->vacancy->link ?: '#' ?>"><?= $invite->getVacancyLabel() ?></a>.
            </p>
            <?php if (empty($invite->receiverUser)): ?>
            <p>
                <a href="<?= $this->createAbsoluteUrl('static/pages/product') ?>">«Базовый менеджмент»</a> - это деловая симуляция, позволяющая оценить менеджерские навыки в форме увлекательной игры.
            </p>
            <?php endif; ?>
        </div>
        <div class="row">
            <?php echo $form->labelEx($invite, 'message text'); ?>
            <?php echo $form->textArea($invite, 'message', ['rows' => 10, 'cols' => 60]); ?>
            <?php echo $form->error($invite, 'message'); ?>
        </div>
        <div class="row">
            <p>
            <?php if ($invite->receiverUser): ?>
                Пожалуйста, <a href="<?= $this->createAbsoluteUrl('dashboard') ?>">зайдите</a> в свой кабинет и примите приглашение на тестирование для прохождения симуляции.
            <?php else: ?>
                Пожалуйста, <a href="<?= $invite->getInviteLink() ?>">зарегистрируйтесь</a> и в своем кабинете примите приглашение на тестирование для прохождения симуляции.
            <?php endif; ?>
            </p>
        </div>
        <div class="row">
            <?php echo $form->labelEx($invite, 'signature'); ?>
            <?php echo $form->textField($invite, 'signature'); ?>
            <?php echo $form->error($invite, 'signature'); ?>
        </div>
        <div class="row buttons">
            <?php echo CHtml::submitButton('Отправить', ['name' => 'send']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>


<script type="text/javascript">
    $(function() {
        // @link: http://jqueryui.com/dialog/
        $( ".message_window" ).dialog({
            modal: true,
            width: 820,
            open: function( event, ui ) { Cufon.refresh(); }
        });

        $( ".message_window").parent().addClass('nice-border cabmessage');
        $( ".message_window").dialog('open');
    });
</script>
<?php endif; ?>

<?php if ($display_results_for): ?>
    <script type="text/javascript">
        $(function() {
            showSimulationDetails('/simulations/details/<?= $display_results_for->id ?>');
        });
    </script>
<?php endif; ?>

        <!-- simulations-counter-box -->
        <div id="simulations-counter-box" class="nice-border backgroud-light-blue">
            <?php $this->renderPartial('_simulations_counter_box', []) ?>
        </div>

        <div class="sidefeedback"><a href="#" class="light-btn feedback">Обратная связь</a></div>
    </aside>
    <div class="narrow-contnt">
        <!-- corporate-invitations-list-box -->
        <div id="corporate-invitations-list-box" class="transparent-boder wideblock">
            <?php $this->renderPartial('_corporate_invitations_list_box', [
                'inviteToEdit'    => $inviteToEdit,
                'vacancies'       => $vacancies,
            ]) ?>
        </div>


    </div>

</section>