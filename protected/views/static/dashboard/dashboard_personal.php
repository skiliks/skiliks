
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

        <a href="#" data-href="/simulation/promo/lite/<?= $notUsedLiteSimulationInvite->id ?>" style="margin-top:-5px;"
           class="start-lite-simulation-btn start-lite-simulation-btn-no-margin light-btn">Пройти демо (15 мин)
        </a>

        <h1 class="thetitle received-invites-personal">Полученные приглашения</h1>
        <div class="change-simulation-result-render ProximaNova-Bold" style="margin-top:-15px;">
            <?php if($user->profile->assessment_results_render_type == "standard") : ?>
                Относительный рейтинг
            <?php else : ?>
                Абсолютный рейтинг
            <?php endif ?>
        </div>
        <?php
            // _private_invitations_list_box
        $this->renderPartial('_private_invitations_list_box', [])
        ?>
    </div>

    <aside>
        <h2 class="thetitle bigtitle">Личный кабинет</h2>

        <!-- dashboard-skills-box -->
        <div id="dashboard-skills-box" class="nice-border backgroud-rich-blue sideblock">

            <?php $this->renderPartial('_dashboard_skills_box', ['simulation'=>$simulation]) ?>

            <?php if ($display_results_for
                && null !== $simulation
                && $simulation->isAllowedToSeeResults(Yii::app()->user->data())): ?>
                <script type="text/javascript">
                    $(function() {
                        showSimulationDetails('/dashboard/simulationdetails/<?= $display_results_for->id ?>');
                    });
                </script>
            <?php endif; ?>
        </div>

        <?php $this->renderPartial('partials/accept-invite-warning-popup', []) ?>
        <?php $this->renderPartial('partials/pre-start-popup', []) ?>

        <div id="simulation-details-pop-up"></div>

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


