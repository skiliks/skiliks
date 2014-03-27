
<section>

    <!-- HEADER -->

    <section class="page-title-box column-full pull-content-right">

        <h1 class="inline-block pull-left reset-line-height">Личный кабинет</h1 -->
        <h3 class="inline-block pull-left reset-line-height">Полученные приглашения</h3>

        <span data-href="/simulation/promo/lite/<?= $notUsedLiteSimulationInvite->id ?>"
              class="button-white button-white-hover inter-active label icon-arrow-blue
           action-open-lite-simulation-popup">
            Пройти демо (<?= Yii::app()->params['demoDuration'] ?> мин)</span>


    </section>

    <!-- LEFT SIDE -->

    <aside class="column-1-3 pull-content-left inline-block vertical-align-top mark-up-block">
        <label class="mark-up-label">#Dashboard-personal-column-2-3</label>
        <div class="nice-border border-radius-standard background-dark-blue us-column-1-3-min-height
            column-1-3-condensed box-bottom-standard border-radius-standard">

            <!-- Simulation -->
            <?php $this->renderPartial('partials/_dashboard_skills_box', [
                'simulation' => $simulation,
                'user'       => $user,
            ]) ?>
        </div>

        <!-- FEED BACK -->
        <div>
            <span class="action-feedback inter-active button-white
            button-white-hover label icon-arrow-blue">
                Обратная связь
            </span>
        </div>
    </aside>

    <!-- CONTENT -->

    <section class="
        locator-corporate-invitations-list-box
        corporate-invitations-list-box column-2-3-fixed
        pull-content-right inline-block vertical-align-top mark-up-block">

        <label class="mark-up-label">#Dashboard-column-2-3</label>
        <div class="nice-border border-radius-standard reset-padding invitations-table">

            <?php // .table-head используется чтоб нарисовать скругление углов ?>
            <div class="table-head"></div>

            <?php $this->renderPartial('_private_invitations_list_box') ?>

            <?php // .table-footer используется чтоб нарисовать скругление углов ?>
            <div class="table-footer"></div>
        </div>

        <div class="pull-content-right">
            <span class="inter-active color-ffffff action-show-sim-rules">
                Правила прохождения симуляции
            </span>
        </div>

        <div class="pager-place locator-pager-place"></div>

        <?php $this->renderPartial('partials/accept-invite-warning-popup', []) ?>

    </section>
</section>

<?php $this->renderPartial('partials/_exists-simulation-in-progress-warning-popup', []) ?>

<?php if ($display_results_for): ?>
    <?php $this->renderPartial('//global_partials/_popup_result_simulation_container', [
        'display_results_for' => $display_results_for
    ]) ?>
<?php endif; ?>

<?php // margin-bottom 150px ?>
<div class="clearfix column-full"></div>


