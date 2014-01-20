<!-- dashboard corpdashboard -->
<section>
    <!-- TITLE -->
    <section class="page-title-box column-full pull-content-right">
        <h1 class="inline-block pull-left reset-line-height"><?php echo Yii::t('site', 'Work dashboard') ?>
        </h1>

        <!-- start-full-simulation start-full-simulation-btn light-btn -->
        <a href="#" data-href="/simulation/promo/full/<?= $notUsedFullSimulationInvite->id ?>"
           class="button-white label icon-arrow-blue">
            Начать симуляцию (2 часа)
        </a>

        <!-- start-lite-simulation-btn light-btn -->
        <a href="#" data-href="/simulation/promo/lite/<?= $notUsedLiteSimulationInvite->id ?>"
           class="button-white label icon-arrow-blue">
            Пройти демо (<?= Yii::app()->params['demoDuration'] ?> мин)</a>
    </section>

    <!-- LEFT SIDE -->
    <aside class="column-1-3 pull-content-left inline-block vertical-align-top">
        <!-- #invite-people-box nice-border backgroud-rich-blue sideblock-->
        <div class="invite-people-box nice-border background-dark-blue column-1-3-condensed box-standard">
            <?php $this->renderPartial('_invite_people_box', [
                'invite'    => $invite,
                'vacancies' => $vacancies,
            ]) ?>

            <?php if ($display_results_for): ?>
                <?php $this->renderPartial('//global_partials/_popup_result_simulation_container', [ 'display_results_for' => $display_results_for]) ?>
            <?php endif; ?>
        </div>

        <!-- #simulations-counter-box nice-border backgroud-light-blue -->
        <div class="simulations-counter-box background-sky nice-border column-1-3-condensed">
            <?php $this->renderPartial('_simulations_counter_box', []) ?>
        </div>

        <!-- FEED BACK -->
        <!-- sidefeedback light-btn feedback -->
        <br/>
        <div>
            <span class="action-feedback button-white label icon-arrow-blue">
                Обратная связь
            </span>
        </div>
    </aside>

    <!-- TABLE -->
    <!-- .narrow-contnt -->
    <section class="corporate-invitations-list-box column-2-3 pull-content-right inline-block vertical-align-top">

        <!-- #corporate-invitations-list-box transparent-boder wideblock -->
        <div class="nice-border reset-padding column-2-3-condensed">

            <!-- corporate-invitations-list-box -->
            <!-- hack for taking position -->
            <!-- #corporate-invitations-list-box-position -->
            <div class="table-head"></div>

            <?php $this->renderPartial('_corporate_invitations_list_box', [
                'inviteToEdit'    => $inviteToEdit,
                'vacancies'       => $vacancies,
                'user'            => $user,
            ]) ?>
        </div>

        <a class="link-to-loading pull-left link-regular additional-dashboard-link"
        href="/profile/save-analytic-file-2?version=v1">
            <i class="excel-icon"></i>
            <div>Результаты</div>
        </a>

        <a class="link-to-loading pull-right link-regular additional-dashboard-link"
           href="<?= $this->getAssetsUrl() ?>/instructions/Assessment_key_file.pdf">
            <i class="doc-icon"></i>
            <div>Инструкция по оценке</div>
        </a>
    </section>
</section>

<!-- HIDDEN CONTENT { -->

<section>
    <?php $this->renderPartial('partials/accept-invite-warning-popup', []) ?>

    <?php if($show_user_referral_popup) : ?>
        <?php $this->renderPartial('partials/_referrals-popup', []) ?>
    <?php endif; ?>

    <?php if($is_display_tariff_expire_pop_up) : ?>
        <?php $this->renderPartial('partials/_tariff_expire_pop_up', ["hasOtherPopup" => $show_user_referral_popup, 'user' => $user]) ?>
    <?php endif; ?>

    <?php $this->renderPartial('partials/exists-self-to-self-simulation-warning-popup', []) ?>
    <?php $this->renderPartial('partials/pre-start-popup', []) ?>

    <div id="start-trial-full-scenario-pop-up" style="display: none;">
        <div>
            После начала симуляции количество доступных вам приглашений уменшиться на одно.
        </div>

        <a href="" class="light-btn start-trial-full-scenario-agree">Я согласен</a>
        <a href="" class="light-btn start-trial-full-scenario-disagree">Отменить</a>
    </div>


    <script>
        $(document).ready(function() {
            $("#corporate-invitations-list-box").show();
        });
    </script>

    <?php if (true === $validPrevalidate): ?>
        <div class="form form-invite-message message_window" style="display:none;" title="Сообщение">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'send-invite-message-form',
            )); ?>

            <?php echo $form->hiddenField($invite, 'firstname'); ?>
            <?php echo $form->hiddenField($invite, 'lastname'); ?>
            <?php echo $form->hiddenField($invite, 'email'); ?>
            <?php echo $form->hiddenField($invite, 'vacancy_id'); ?>

            <div class="block-form">
                <p><?php echo $form->textField($invite, 'fullname'); ?></p>

                <?php if (Yii::app()->params['emails']['isDisplayStandardInvitationMailTopText']): ?>
                    <p class="font-green-dark">Компания <?= $invite->ownerUser->account_corporate->company_name ?> предлагает вам пройти тест "Базовый менеджмент".</p>
                    <?php if (empty($invite->receiverUser)): ?>
                        <p class="font-green-dark">
                            <a target="_blank" href="<?= $this->createAbsoluteUrl('static/pages/product') ?>">"Базовый менеджмент"</a>
                            - это деловая симуляция, позволяющая оценить менеджерские навыки в форме увлекательной игры.</p>
                    <?php endif; ?>
                <?php endif; ?>

                <p><?php echo $form->textArea($invite, 'message', ['rows' => 20, 'cols' => 60]); ?><?php echo $form->error($invite, 'message'); ?></p>
                <p class="font-green-dark">
                    <?php if ($invite->receiverUser && !$invite->receiverUser->isActive()): ?>
                        Пожалуйста, <a target="_blank" href="<?=$invite->receiverUser->getActivationUrl()?>">активируйте ваш аккаунт</a>,
                        выберите индивидуальный профиль, ввойдите в свой кабинет
                        и примите приглашение на тестирование для прохождения симуляции.
                    <?php elseif ($invite->receiverUser && $invite->receiverUser->isPersonal()): ?>
                        Пожалуйста, <a target="_blank" href="<?= $this->createAbsoluteUrl('/dashboard') ?>">зайдите</a> в свой кабинет и примите приглашение на тестирование для прохождения симуляции.
                    <?php elseif ($invite->receiverUser && $invite->receiverUser->isCorporate()): ?>
                        Пожалуйста, <a target="_blank" href="<?= $this->createAbsoluteUrl('/registration') ?>">создайте личный профиль</a> или
                        <a href="<?= $this->createAbsoluteUrl('/dashboard') ?>">войдите в личный кабинет</a> и примите приглашение на тестирование для прохождения симуляции.
                    <?php else: ?>
                        Пожалуйста, <a target="_blank" href="<?= $this->createAbsoluteUrl('/registration') ?>">зарегистрируйтесь</a> и примите приглашение на тестирование для прохождения симуляции.
                    <?php endif; ?>
                </p>
                <p class="font-green-dark">Ваш skiliks</p>
            </div>

            <div class="inline-block">
                <?php echo $form->checkBox($invite, 'is_display_simulation_results', ['class'=>'inline-radio-button']); ?>
                <?php echo $form->labelEx($invite, 'is_display_simulation_results', ['class'=>'inline-radio-button-label']); ?>
            </div>
            <div style="clear:both;"></div>
            <div class="row buttons no-margin-left">
                <?php echo CHtml::submitButton('Отправить', ['name' => 'send']); ?>
            </div>

            <?php $this->endWidget(); ?>
        </div>
        <script>
            // @link: http://jqueryui.com/dialog/
            $(document).ready(function() {
                $( ".message_window" ).dialog({
                    modal: true,
                    resizable: false,
                    draggable: false,
                    width: 590,
                    height: 500,
                    position: {
                        my: "left top",
                        at: "left top",
                        of: $('#corporate-invitations-list-box')
                    }
                });

                $( ".message_window").parent().addClass('nice-border cabmessage');
                $( ".message_window").dialog('open', $("#corporate-invitations-list-box").show());
            });
        </script>
    <?php endif; ?>
</section>
<!-- } hidden content -->


