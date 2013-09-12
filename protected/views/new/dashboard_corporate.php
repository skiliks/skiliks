<div class="container-borders-3">
    <h1 class="page-header margin-less"><?php echo Yii::t('site', 'Work dashboard') ?></h1>
    <div class="grid1">
    <div id="invite-people-box" class="block-border bg-rich-blue border-large">
    <!-- invite-people-box -->
     <?php $this->renderPartial('//new/_invite_people_box', [
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

        <div class="block-form invite-form-block">
            <p><?php echo $form->textField($invite, 'fullname'); ?></p>

            <?php if (Yii::app()->params['emails']['isDisplayStandardInvitationMailTopText']): ?>
                <p class="font-green-dark">Компания <?= $invite->ownerUser->account_corporate->company_name ?> предлагает вам пройти тест «Базовый менеджмент» на позицию
                    <a target="_blank" href="<?= $invite->vacancy->link ?: '#' ?>"><?= $invite->getVacancyLabel() ?></a>.</p>
                <?php if (empty($invite->receiverUser)): ?>
                    <p class="font-green-dark">
                        <a target="_blank" href="<?= $this->createAbsoluteUrl('static/pages/product') ?>">«Базовый менеджмент»</a>
                        - это деловая симуляция, позволяющая оценить менеджерские навыки в форме увлекательной игры</p>
                <?php endif; ?>
            <?php endif; ?>

            <p><?php echo $form->textArea($invite, 'message', ['rows' => 10, 'cols' => 60]); ?><?php echo $form->error($invite, 'message'); ?></p>
            <p class="font-green-dark">
                <?php if ($invite->receiverUser && $invite->receiverUser->isPersonal()): ?>
                    Пожалуйста, <a target="_blank" href="<?= $this->createAbsoluteUrl('/dashboard') ?>">зайдите</a> в свой кабинет и примите приглашение на тестирование для прохождения симуляции.
                <?php elseif ($invite->receiverUser && $invite->receiverUser->isCorporate()): ?>
                    Пожалуйста, <a target="_blank" href="<?= $this->createAbsoluteUrl('/registration') ?>">создайте личный профиль</a> или
                    <a href="<?= $this->createAbsoluteUrl('/dashboard') ?>">войдите в личный кабинет</a> и примите приглашение на тестирование для прохождения симуляции.
                <?php else: ?>
                    Пожалуйста, <a target="_blank" href="<?= $this->createAbsoluteUrl('/registration') ?>">зарегистрируйтесь</a> или <a href="<?= $this->createAbsoluteUrl('/user/auth') ?>">войдите</a> в свой кабинет и примите приглашение на тестирование для прохождения симуляции.
                <?php endif; ?>
            </p>
            <p class="font-green-dark">Ваш skiliks</p>
        </div>

            <?php // echo $form->labelEx($invite, 'signature'); ?>
            <?php // echo $form->textField($invite, 'signature'); ?>
            <?php // echo $form->error($invite, 'signature'); ?>
        <div class="row buttons no-margin-left">
            <?php echo CHtml::submitButton('Отправить', ['name' => 'send']); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>



<script type="text/javascript">
    $(function() {
        // @link: http://jqueryui.com/dialog/
        $( ".message_window" ).dialog({
            modal: true,
            resizable: false,
            draggable: false,
            width: 605, /*590 */
            height: 500,
            position: {
                my: "left top",
                at: "left top",
                of: $('#corporate-invitations-list-box .items')
            },
            open: function( event, ui ) { Cufon.refresh(); }
        });

        $( ".message_window").parent().addClass('popup-primary popup-site title-in-ui submit-primry cabmessage');
        $( ".message_window").dialog('open');
    });
</script>
<?php endif; ?>

<?php if ($display_results_for): ?>
    <script type="text/javascript">
        $(function() {
            showSimulationDetails('/dashboard/simulationdetails/<?= $display_results_for->id ?>');
        });
    </script>
<?php endif; ?>

        <!-- simulations-counter-box -->
        <div id="simulations-counter-box" class="block-border bg-light-blue">
            <div class="pad-large">
                <?php $this->renderPartial('//new/_simulations_counter_box', []) ?>
            </div>
        </div>

        <a href="#" class="btn btn-primary feedback">Обратная связь</a>
    </div>
    <div id="corporate-invitations-list-box" class="block-border grid2 border-primary dashboard">
        <!-- corporate-invitations-list-box -->
        <?php $this->renderPartial('//new/_corporate_invitations_list_box', [
            'inviteToEdit'    => $inviteToEdit,
            'vacancies'       => $vacancies,
        ]) ?>
    </div>
</div>