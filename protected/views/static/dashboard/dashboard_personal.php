
<section class="dashboard">
    <h2 class="thetitle"><?php echo Yii::t('site', 'Dashboard') ?></h2>

    <!-- private-invitations-list-box -->
    <div id="private-invitations-list-box" class="nice-border backgroud-light-yellow wideblock">
        <?php $this->renderPartial('_private_invitations_list_box', []) ?>
    </div>

    <aside>
        <!-- dashboard-skills-box -->
        <div id="dashboard-skills-box" class="nice-border backgroud-rich-blue">
            <?php $this->renderPartial('_dashboard_skills_box', []) ?>
            <p>Core management <span class="rating">60%</span><a href="#" class="link-go">Go</a></p>
        </div>

        <!-- companies-you-follow-box -->
        <div id="companies-you-follow-box" class="backgroud-bue-bordered">
            <?php $this->renderPartial('_companies_you_follow_box', []) ?>
        </div>

        <!-- job-recomendations-box -->
        <div id="job-recomendations-box" class="backgroud-bue-bordered">
            <?php $this->renderPartial('_job_recomendations_box', []) ?>
        </div>
    </aside>
</section>


