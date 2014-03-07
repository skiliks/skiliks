<?php
/** @var Simulation $simulation
 * @var YumUser $user
 * @var Invite $invite
 */
?>
<?php if($simulation->game_type->isLite()) : ?>
    <h1>Пример отчета по оценке (цифры случайные)</h1>
<?php elseif($user->isPersonal()) : ?>
    <h1 class="name"><?php echo $user->profile->firstname ?> <?php echo $user->profile->lastname ?></h1>
<?php elseif(null === $simulation->invite) : ?>
    <?php // это хак для просмотра результатов lite симуляций,
    //в случае одновременного запуска нескольких lite симуляций по одному и туме же инвайту  ?>
    <h1 class="name"><?php echo $user->profile->firstname ?> <?php echo $user->profile->lastname ?></h1>
<?php else : ?>
    <h1 class="name"><?php echo $simulation->invite->firstname ?> <?php echo $simulation->invite->lastname ?></h1>
<?php endif ?>

<div class="simulation-details scenario-<?= $simulation->game_type->slug ?>">
    <script type="text/javascript">
        var AR = <?= $details; ?>;

        function drawChartBlock(classPrefix, data, codes) {
            var i, k;
            for (k = 1; k <= 2 * codes.length; k++) {
                i = Math.ceil(k / 2);
                new charts.Bar(
                    '.' + classPrefix + '-' + i + ' .' + (k % 2 ? 'chartbar' : 'chartproblem'),
                    Math.round(data && data[codes[i - 1]] ? data[codes[i - 1]][k % 2 ? '+' : '-'] : 0),
                    { valueRenderer: function(v) { return v + '%';}, 'class': (k % 2 ? '' : 'redbar') }
                );
            }
        }
    </script>

    <div class="navigatnwrap scenario-<?= $simulation->game_type->slug ?>-box">
        <ul class="navigation">
            <li><a href="#main"><?php echo Yii::t('site', 'Main') ?></a></li>
            <li><a href="#time-management"><?php echo Yii::t('site', 'Time management') ?></a></li>
            <li><a href="#productivity"><?php echo Yii::t('site', 'Productivity') ?></a></li>
            <li><a href="#managerial-skills"><?php echo Yii::t('site', 'Managerial skills') ?></a></li>
            <?php /* not in release 1.2
                <li><a href="#personal-qualities"><?php echo Yii::t('site', 'Personal qualities') ?></a></li>
            */ ?>
        </ul>
    </div>

    <div class="sections">
        <div id="main">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_main', [
                'data' => json_decode($details, true)['additional_data']
            ]) ?>
        </div>

        <div id="time-management">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_time_management', []) ?>
        </div>

        <div id="time-management-detail">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_time_management_detail', []) ?>
        </div>

        <div id="productivity">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_productivity') ?>
        </div>

        <div id="managerial-skills">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_managerial_skills', []) ?>
        </div>

        <div id="managerial-skills-1">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_managerial_skills_1', ['simulation'=>$simulation]) ?>
        </div>

        <div id="managerial-skills-2">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_managerial_skills_2', []) ?>
        </div>

        <div id="managerial-skills-3">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_managerial_skills_3', []) ?>
        </div>
    </div>

    <div class="estmfooter">
        <a class="prev" href="#prev"><?php echo Yii::t('site', 'Back') ?></a>
        <?php if($simulation->isFull()) : ?>
            <a class="fullreport" href="/pdf/simulation-detail-pdf/<?= $simulation->id ?>/<?= $simulation->assessment_version ?>"><?php echo Yii::t('site', 'Полный отчет') ?></a>
        <?php endif ?>
        <a class="next" href="#next"><?php echo Yii::t('site', 'Next') ?></a>
    </div>
</div>



