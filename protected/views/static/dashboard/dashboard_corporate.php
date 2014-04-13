<section>
    <!-- TITLE -->
    <section class="page-title-box column-full pull-content-right">
        <h1 class="inline-block pull-left reset-line-height">
            <?php echo Yii::t('site', 'Work dashboard') ?>
        </h1>

        <span data-href="/simulation/promo/lite/<?= $notUsedLiteSimulationInvite->id ?>"
           class="button-white button-white-hover inter-active label icon-arrow-blue
           action-open-lite-simulation-popup">
            Пройти демо (<?= Yii::app()->params['demoDuration'] ?> мин)</span>

        <span data-href="/simulation/promo/full/<?= $notUsedFullSimulationInvite->id ?>"
              class="button-white button-white-hover inter-active label icon-arrow-blue
              action-open-full-simulation-popup">
            Начать симуляцию (2 часа)
        </span>
    </section>

    <!-- LEFT SIDE -->
    <aside class="column-1-3 pull-content-left inline-block vertical-align-top mark-up-block">
        <label class="mark-up-label">#Dashboard-aside</label>
        <div class="invite-people-box nice-border border-radius-standard background-dark-blue
            us-column-1-3-min-height column-1-3-condensed box-bottom-standard border-radius-standard">
            <?php $this->renderPartial('_invite_people_box', [
                'invite'    => $invite,
                'vacancies' => $vacancies,
                'user' => $user
            ]) ?>

            <?php if ($display_results_for): ?>
                <?php $this->renderPartial('//global_partials/_popup_result_simulation_container', [ 'display_results_for' => $display_results_for]) ?>
            <?php endif; ?>
        </div>

        <div class="simulations-counter-box background-sky border-radius-standard nice-border column-1-3-condensed">
            <?php $this->renderPartial('_simulations_counter_box', []) ?>
        </div>

        <!-- FEED BACK -->
        <br/>
        <div>
            <span class="action-feedback inter-active button-white reset-margin
            button-white-hover label icon-arrow-blue">
                Обратная связь
            </span>
        </div>
    </aside>

    <!-- TABLE -->
    <section class="
        locator-corporate-invitations-list-box
        corporate-invitations-list-box column-2-3-fixed
        pull-content-right inline-block vertical-align-top mark-up-block">

        <label class="mark-up-label">#Dashboard-column-2-3</label>
        <div class="nice-border border-radius-standard reset-padding invitations-table">

            <?php // .table-head используется чтоб нарисовать скругление углов ?>
            <div class="table-head"></div>

            <?php $this->renderPartial('_corporate_invitations_list_box', [
                'inviteToEdit'    => $inviteToEdit,
                'vacancies'       => $vacancies,
                'user'            => $user,
            ]) ?>

            <?php // .table-footer используется чтоб нарисовать скругление углов ?>
            <div class="table-footer"></div>
        </div>

        <a class="action-load-file pull-left label link-with-background"
        href="/profile/save-full-assessment-analytic-file">
            <i class="icon icon-excel"></i>
            <label>Результаты</label>
        </a>

        <a class="action-load-file label link-with-background"
           href="<?= $this->getAssetsUrl() ?>/instructions/Assessment_key_file.pdf">
            <i class="icon icon-doc"></i>
            <label>Инструкция по оценке</label>
        </a>

        <div class="pager-place locator-pager-place"></div>

    </section>
</section>

<!-- ------------------------------------------------------------------------------------- -->
<!-- HIDDEN CONTENT { -->

<section>
    <?php $this->renderPartial('partials/accept-invite-warning-popup', []) ?>

    <?php $this->renderPartial('partials/_exists-simulation-in-progress-warning-popup', []) ?>

    <?php if (true === $validPrevalidate): ?>
        <?php // отправить приглашение шаг 2 ?>
        <?php $this->renderPartial('partials/_invite_people_popup', [
            'invite' => $invite,
            'isDisplayStandardInvitationMailTopText' => $isDisplayStandardInvitationMailTopText
        ]) ?>
    <?php endif; ?>
</section>
<!-- } hidden content -->

<?php // margin-bottom 200px ?>
<div class="clearfix column-full"></div>

