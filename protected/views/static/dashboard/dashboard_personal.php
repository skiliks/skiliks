
<section class="dashboard">
    <h1><?php echo Yii::t('site', 'Dashboard') ?></h1>

    <br/>

    <!-- dashboard-skills-box -->
    <div id="dashboard-skills-box" class="nice-border backgroud-rich-blue">
        <?php $this->renderPartial('_dashboard_skills_box', []) ?>
    </div>

    <!-- private-invitations-list-box -->
    <div id="private-invitations-list-box" class="nice-border backgroud-light-yellow">
        <?php $this->renderPartial('_private_invitations_list_box', []) ?>
    </div>

    <!-- companies-you-follow-box -->
    <div id="companies-you-follow-box" class="backgroud-bue-bordered">
        <?php $this->renderPartial('_companies_you_follow_box', []) ?>
    </div>

    <!-- job-recomendations-box -->
    <div id="job-recomendations-box" class="backgroud-bue-bordered">
        <?php $this->renderPartial('_job_recomendations_box', []) ?>
    </div>
</section>


