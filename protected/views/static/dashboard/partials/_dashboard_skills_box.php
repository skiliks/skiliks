<div class="pull-content-right">
    <h2 class="pull-left">Мои навыки</h2>

    <?php $scoreName = ($user->profile->assessment_results_render_type == "standard")
        ? "stars-selected" : "percentile-selected"; ?>

    <!-- Переключение между относительным и абсолютным рейтингом. -->
    <span class="action-switch-assessment-results-render-type
        action-display-assessment-results-type-hint
        locator-assessment-results-type-switcher
        assessment-results-type-switcher inter-active <?= $scoreName ?>"></span>
</div>

<div class="hint-assessment-results-type-switcher inner-popover background-yellow hide locator-hint-assessment-results-type-switcher">
    <div class="popover-triangle-upper"></div>
    <div class="popover-wrapper">
        <div class="popover-content">
            Переключение между относительным и абсолютным рейтингом.
        </div>
    </div>
</div>

<?php
    if (null !== $simulation
        && true === $simulation->isAllowedToSeeResults(Yii::app()->user->data())) {
        $this->renderPartial('//global_partials/_simulation_stars', [
            'simulation'  => $simulation,
            'isSkillsBox' => true
        ]);
    }
?>

