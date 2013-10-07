
<?php
    if (false == isset($isDisplayTitle)) { $isDisplayTitle = true; }
    if (false == isset($isDisplayArrow)) { $isDisplayArrow = true; }
    if (false == isset($isDisplayScaleIfSimulationNull)) { $isDisplayScaleIfSimulationNull = true; }

    if (false == $isDisplayScaleIfSimulationNull && null == $simulation) { return; };
    $isSimComplete = (null !== $simulation && $simulation->end !== null && $simulation->isFull());
/* @var $simulation Simulation */
?>

<?php $user = Yii::app()->user->data(); ?>

<?php if($user->profile->assessment_results_render_type == "standard") :?>
    <p>
        <?php if ($isSimComplete): ?>
            <?php if($isDisplayTitle): ?>
                <span class="skillstitle">Базовый менеджмент</span>
            <?php endif ?><span <?php echo 'data-simulation="/dashboard/simulationdetails/'.$simulation->id.'"'; ?> class="ratingwrap radiusthree <?php if($simulation->end !== null) { echo "view-simulation-details-pop-up";} ?>"><span class="ratebg block-stars"><span class="rating block-stars" style="width: <?php echo $simulation->getCategoryAssessment(); ?>%"></span></span><span class="prcentval block-stars"><?= $simulation->getCategoryAssessment() ?>%</span><!-- </sup> --></span>
            <?php if ($isDisplayArrow) : ?>
                <a href="#" data-simulation="/dashboard/simulationdetails/<?php echo $simulation->id; ?>" class="link-go view-simulation-details-pop-up"></a>
            <?php endif ?>
        <?php endif; ?>
    </p>
<?php else : ?>

        <?php if ($isSimComplete): ?>
                <?php if($isDisplayTitle): ?>
                    <p style="float:left;"><span class="skillstitle">Базовый менеджмент</span>
                <?php endif ?>
            <div <?php echo 'data-simulation="/dashboard/simulationdetails/'.$simulation->id.'"'; ?> class="percentil_overall_container percentil_dashboard_container <?php if($simulation->end !== null) { echo "view-simulation-details-pop-up";} ?>"><span class="percentil_base" style="text-align: right;"><span class="percentil_overall" style="width:<?=round($simulation->invite->getPercentile())?>%;"></span></span><span class="percentile_dashboard_value ProximaNova-Bold">P<?=round($simulation->invite->getPercentile())  ?></span></div><?php if ($isDisplayArrow) : ?>
                <a href="#" data-simulation="/dashboard/simulationdetails/<?php echo $simulation->id; ?>" class="link-go view-simulation-details-pop-up link-go-percentile"></a>
                <?php if($isDisplayTitle): ?>
                    </p>
                <?php endif ?>
            <?php endif ?>
        <?php endif; ?>
<?php endif; ?>