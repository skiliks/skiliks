<div class="pull-content-right">
    <h2 class="pull-left">Мои навыки</h2>

    <?php $scoreName = ($user->profile->assessment_results_render_type == "standard")
        ? "stars-selected" : "percentile-selected"; ?>

    <!-- Переключение между относительным и абсолютным рейтингом. -->
    <span class="action-switch-assessment-results-render-type
                    assessment-results-type-switcher inter-active <?= $scoreName ?>"></span>

    <div class="popover popover-div-on-hover hide">
        <div class="popover-triangle"></div>
        <div class="popover-content"><div class="popup-content">
                Переключение между относительным и абсолютным рейтингом.
            </div>
        </div>
    </div>
</div>
<?
$scoreName = ($user->profile->assessment_results_render_type == "standard") ? "percentile-toggle-off" : "percentile-toggle-on";
?>

<span class="change-simulation-result-render percentile-hover-toggle-span dashboard-personal-change-percentile <?=$scoreName?>"></span>
<?php
    if (null !== $simulation
        && true === $simulation->isAllowedToSeeResults(Yii::app()->user->data())) {
        $this->renderPartial('//global_partials/_simulation_stars', [
            'simulation'  => $simulation,
            'isSkillsBox' => true
        ]);
    }
?>

