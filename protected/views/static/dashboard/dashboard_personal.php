
<section>

    <!-- HEADER -->

    <section class="page-title-box column-full pull-content-right">

        <h1 class="inline-block pull-left reset-line-height">Личный кабинет</h1 -->
        <h3 class="inline-block pull-left reset-line-height">Полученные приглашения</h3>

        <span data-href="/simulation/promo/lite/<?= $notUsedLiteSimulationInvite->id ?>"
              class="button-white inter-active label icon-arrow-blue
           action-open-lite-simulation-popup">
            Пройти демо (<?= Yii::app()->params['demoDuration'] ?> мин)</span>


    </section>

    <!-- LEFT SIDE -->

    <aside class="column-1-3 pull-content-left inline-block vertical-align-top mark-up-block">
        <label class="mark-up-label">#Dashboard-personal-column-2-3</label>
        <div class="nice-border border-radius-standard background-dark-blue
            column-1-3-condensed box-bottom-standard border-radius-standard">

            <!-- Simulation -->
            <?php $this->renderPartial('partials/_dashboard_skills_box', [
                'simulation' => $simulation,
                'user'       => $user,
            ]) ?>
        </div>

        <!-- FEED BACK -->
        <div>
            <span class="action-feedback inter-active button-white label icon-arrow-blue reset-margin">
                Обратная связь
            </span>
        </div>
    </aside>

    <!-- CONTENT -->

    <section class="
        locator-corporate-invitations-list-box
        corporate-invitations-list-box column-2-3-wide
        pull-content-right inline-block vertical-align-top mark-up-block">

        <label class="mark-up-label">#Dashboard-column-2-3</label>
        <div class="nice-border border-radius-standard reset-padding invitations-table">

            <?php // .table-head используется чтоб нарисовать скругление углов ?>
            <div class="table-head"></div>

            <?php $this->renderPartial('_private_invitations_list_box') ?>

            <?php // .table-footer используется чтоб нарисовать скругление углов ?>
            <div class="table-footer"></div>
        </div>

        <div class="pager-place locator-pager-place"></div>

        <?php $this->renderPartial('partials/accept-invite-warning-popup', []) ?>

    </section>

    <?php /*

    <div class="popover hide popover-div-on-hover dashboard-personal-change-percentile">
        <div class="popover-triangle hide"></div>
        <div class="popover-content">
            <div class="popup-content">Переключение между относительным и абсолютным рейтингом.</div>
        </div>
    </div>

        <div class="change-simulation-result-render personal-button">
            <?php if($user->profile->assessment_results_render_type == "standard") : ?>
                Относительный рейтинг
            <?php else : ?>
                Абсолютный рейтинг
            <?php endif ?>
        </div>
        <div style="clear:both; min-height: 2px;"></div>
        <?php
            // _private_invitations_list_box
        $this->renderPartial('_private_invitations_list_box', [])
        ?>

        <div class="show-simulation-rules">
            <span>Правила прохождения симуляции</span>
        </div>



    <aside>


        <!-- dashboard-skills-box -->
        <div id="dashboard-skills-box" class="border-radius-standard nice-border backgroud-rich-blue sideblock">



            <?php if ($display_results_for
                && null !== $simulation
                && $simulation->isAllowedToSeeResults(Yii::app()->user->data())): ?>
                <?php $this->renderPartial('//global_partials/_popup_result_simulation_container', [ 'display_results_for' => $display_results_for]) ?>
            <?php endif; ?>
        </div>



        <div class="sidefeedback"><a href="#" class="light-btn feedback">Обратная связь</a></div>

    </aside>
  */ ?>
</section>

<?php $this->renderPartial('partials/_exists-simulation-in-progress-warning-popup', []) ?>

<?php // margin-bottom 150px ?>
<div class="clearfix column-full"></div>


