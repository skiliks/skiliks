
<section class="dashboard">
    <!-- private-invitations-list-box -->
    <div class="narrow-contnt">
        <div style="clear:both;"></div>
        <?php /* not in release 1
        <div class="searchform">
            <input type="text" class="inputtext" placeholder="Search"/>
            <input type="submit"/>
        </div>
        */?>
        <a href="#" data-href="/simulation/promo/lite/<?= $notUsedLiteSimulationInvite->id ?>"
           class="start-lite-simulation-btn start-lite-simulation-btn-no-margin start-lite-in-personal light-btn">
            Пройти демо (<?= Yii::app()->params['demoDuration'] ?> мин)
        </a>
        <div class="popover popover-div-on-hover dashboard-personal-change-percentile"><div class="popover-triangle"></div><div class="popover-content"><div class="popup-content ProximaNova">Переключение между относительным и абсолютным рейтингом.</div></div></div>
        <h1 class="thetitle received-invites-personal">Полученные приглашения</h1>
        <div class="change-simulation-result-render personal-button ProximaNova-Bold">
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

    </div>

    <aside>
        <h2 class="thetitle bigtitle">Личный кабинет</h2>

        <!-- dashboard-skills-box -->
        <div id="dashboard-skills-box" class="nice-border backgroud-rich-blue sideblock">

            <?php $this->renderPartial('_dashboard_skills_box', ['simulation'=>$simulation, 'user' => $user]) ?>

            <?php if ($display_results_for
                && null !== $simulation
                && $simulation->isAllowedToSeeResults(Yii::app()->user->data())): ?>
                <?php $this->renderPartial('//global_partials/_popup_result_simulation_container', [ 'display_results_for' => $display_results_for]) ?>
            <?php endif; ?>
        </div>

        <?php $this->renderPartial('partials/accept-invite-warning-popup', []) ?>
        <?php $this->renderPartial('partials/pre-start-popup', []) ?>

        <div class="sidefeedback"><a href="#" class="light-btn feedback">Обратная связь</a></div>

        <!-- companies-you-follow-box -->
        <?php /* not in release 1
        <div id="companies-you-follow-box" class="backgroud-bue-bordered sideblock">
            <?php $this->renderPartial('_companies_you_follow_box', []) ?>
        </div>
        */ ?>

        <!-- job-recomendations-box -->
        <?php /* not in release 1
        <div id="job-recomendations-box" class="backgroud-bue-bordered sideblock">
            <?php $this->renderPartial('_job_recomendations_box', []) ?>
        </div>
        */ ?>

        <?php /* not here in release 1
        <div class="pager">
            <a href="#">Prev</a>
        </div>
        */ ?>

    </aside>
</section>

<?php $this->renderPartial('//global_partials/_before_start_lite_simulation_popup', []) ?>

