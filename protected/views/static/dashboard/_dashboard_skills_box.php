<h2>Мои навыки</h2>
<?
$scoreName = ($user->profile->assessment_results_render_type == "standard") ? "percentile-toggle-off" : "percentile-toggle-on";
?>
<span class="change-simulation-result-render percentile-hover-toggle-span dashboard-personal-change-percentile <?=$scoreName?>"></span>
<?php
    if (null !== $simulation
        && true === $simulation->isAllowedToSeeResults(Yii::app()->user->data())) {
        $this->renderPartial('//global_partials/_simulation_stars', [
            'simulation'    => $simulation,
        ]);
    } else {
        echo '<br/>';
    }
?>

<?php /* not in release 1
    <div>
        <a href="#" class="light-btn">Compare</a>
        <a href="#" class="light-btn">Apply to position</a>
    </div>
*/?>