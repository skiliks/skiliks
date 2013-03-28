
<section class="dashboard">
    <!-- private-invitations-list-box -->
    <div class="narrow-contnt">
        <?php /* not in release 1
        <div class="searchform">
            <input type="text" class="inputtext" placeholder="Search"/>
            <input type="submit"/>
        </div>
        */?>

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

        </div>

        <div class="sidefeedback"><a href="#" class="light-btn">Обратная связь</a></div>

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


