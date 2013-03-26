
<section class="dashboard">


    <!-- private-invitations-list-box -->
    <div class="narrow-contnt">
        <h1 class="thetitle">Received invites</h1>
        <div id="private-invitations-list-box" class="nice-border backgroud-light-yellow wideblock">
            <?php $this->renderPartial('_private_invitations_list_box', []) ?>

            <div class="grid-view">
                <table class="items">
                    <thead>
                    <tr>
                        <th>Company</th>
                        <th>Position</th>
                        <th><a href="#">Assestment</a></th>
                        <th><a href="#">Date / time</a></th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Eksmo</td>
                            <td>Head of IT</td>
                            <td>Core management</td>
                            <td>1/09/12  3:02</td>
                            <td><a href="#" class="blue-btn">Accept</a> or <a href="#">Decline</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <aside>
        <h2 class="thetitle"><?php echo Yii::t('site', 'Dashboard') ?></h2>
        <!-- dashboard-skills-box -->
        <div id="dashboard-skills-box" class="nice-border backgroud-rich-blue sideblock">
            <?php $this->renderPartial('_dashboard_skills_box', []) ?>
            <p>Core management
                <span class="ratingwrap radiusthree">
                    <span class="ratebg">
                        <span class="rating" style="width: 80%"></span>
                    </span>
                    80%
                </span>
                <a href="#" class="link-go"></a>
            </p>
            <div><a href="#" class="light-btn">Compare</a><a href="#" class="light-btn">Apply to position</a></div>
        </div>

        <!-- companies-you-follow-box -->
        <div id="companies-you-follow-box" class="backgroud-bue-bordered sideblock">
            <?php $this->renderPartial('_companies_you_follow_box', []) ?>
            <ul class="nodesign-list">
                <li><a href="#">Skiliks <span>1</span></a></li>
                <li><a href="#">Googgle Russia <span>5</span></a></li>
                <li><a href="#">Headhunter</a></li>
                <li><a href="#">Yandex</a></li>
                <li><a href="#">Sberbank</a></li>
            </ul>
        </div>

        <!-- job-recomendations-box -->
        <div id="job-recomendations-box" class="backgroud-bue-bordered sideblock">
            <?php $this->renderPartial('_job_recomendations_box', []) ?>
        </div>
    </aside>
</section>


