<?php
    if (false == isset($isDisplayTitle)) { $isDisplayTitle = true; }
    if (false == isset($isDisplayArrow)) { $isDisplayArrow = true; }
    if (false == isset($isDisplayScaleIfSimulationNull)) { $isDisplayScaleIfSimulationNull = true; }

    if (false == $isDisplayScaleIfSimulationNull && null == $simulation) { return; };
    $isSimComplete = (null !== $simulation && $simulation->end !== null && $simulation->isFull());
/* @var $simulation Simulation */
?>
<p>
    <?php if ($isSimComplete): ?>
        <?php if($isDisplayTitle): ?>
            <span class="skillstitle">Базовый менеджмент</span>
        <?php endif ?>
        <span <?php echo 'data-simulation="/simulations/details/'.$simulation->id.'"'; ?>
            class="ratingwrap radiusthree <?php if($simulation->end !== null) { echo "view-simulation-details-pop-up";} ?>">
            <span class="ratebg"><span class="rating" style="width: <?php echo $simulation->getCategoryAssessment(); ?>%"></span></span><span class="prcentval"><!--<sup>-->
                <?= $simulation->getCategoryAssessment() ?>%
            </span><!-- </sup> --></span>
        <?php if ($isDisplayArrow) : ?>
            <a href="#" data-simulation="/simulations/details/<?php echo $simulation->id; ?>" class="link-go view-simulation-details-pop-up"></a>
        <?php endif ?>
    <?php endif; ?>
</p>