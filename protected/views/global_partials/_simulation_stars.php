
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
    <?= round($simulation->percentile)*100 ?>
<?php endif; ?>