<div class="container-borders-3">
    <h1 class="page-header"><?php echo Yii::t('site', 'Work dashboard') ?></h1>
    <div id="invite-people-box" class="grid1 block-border bg-rich-blue border-large pull-left">
    <!-- invite-people-box -->
     <?php $this->renderPartial('//new/_invite_people_box', [
        'invite'    => $invite,
        'vacancies' => $vacancies,
     ]) ?>

<?php if (true === $validPrevalidate): ?>
    <div class="form form-invite-message message_window" title="Сообщение">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'send-invite-message-form',
        )); ?>

        <?php echo $form->hiddenField($invite, 'firstname'); ?>
        <?php echo $form->hiddenField($invite, 'lastname'); ?>
        <?php echo $form->hiddenField($invite, 'email'); ?>
        <?php echo $form->hiddenField($invite, 'vacancy_id'); ?>

        <div class="block-form">
            <p><?php echo $form->textField($invite, 'fullname'); ?></p>
            <p class="font-green-dark">Компания <?= $invite->ownerUser->account_corporate->company_name ?: 'Компания' ?> предлагает вам пройти тест «Базовый менеджмент» для участия в конкурсе на вакансию <?= $invite->getVacancyLink('') ?>.</p>
            <?php if (empty($invite->receiverUser)): ?>
            <p class="font-green-dark"><a href="<?= $this->createAbsoluteUrl('static/pages/product') ?>">«Базовый менеджмент»</a> - это деловая симуляция, позволяющая оценить менеджерские навыки в форме увлекательной игры</p>
            <?php endif; ?>
            <p><?php echo $form->textArea($invite, 'message', ['rows' => 10, 'cols' => 60]); ?><?php echo $form->error($invite, 'message'); ?></p>
            <p class="font-green-dark">
                <?php if ($invite->receiverUser && $invite->receiverUser->isPersonal()): ?>
                    Пожалуйста, <a href="<?= $this->createAbsoluteUrl('dashboard') ?>">зайдите</a> в свой кабинет и примите приглашение на тестирование для прохождения симуляции.
                <?php elseif ($invite->receiverUser && $invite->receiverUser->isCorporate()): ?>
                    Пожалуйста, <a href="<?= $this->createAbsoluteUrl('/registration') ?>">создайте личный профиль</a> или
                    <a href="<?= $this->createAbsoluteUrl('static/dashboard/personal') ?>">войдите в личный кабинет</a> и примите приглашение на тестирование для прохождения симуляции.
                <?php else: ?>
                    Пожалуйста, <a href="<?= $this->createAbsoluteUrl('/registration') ?>">зарегистрируйтесь</a> или <a href="<?= $this->createAbsoluteUrl('/user/auth') ?>">войдите</a> в свой кабинет и примите приглашение на тестирование для прохождения симуляции.
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
            width: 590,
            height: 500,
            position: {
                my: "left top",
                at: "left top",
                of: $('#corporate-invitations-list-box .items')
            },
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
            <?php $this->renderPartial('//new/_simulations_counter_box', []) ?>
        </div>

        <div class="sidefeedback"><a href="#" class="light-btn feedback">Обратная связь</a></div>
    </div>
    <div id="corporate-invitations-list-box" class="block-border grid2 border-primary dashboard">
        <!-- corporate-invitations-list-box -->
        <?php $this->renderPartial('//new/_corporate_invitations_list_box', [
            'inviteToEdit'    => $inviteToEdit,
            'vacancies'       => $vacancies,
        ]) ?>
    </div>
</div>