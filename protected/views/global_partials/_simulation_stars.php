<?php
    if (false == isset($isDisplayTitle)) { $isDisplayTitle = true; }
    if (false == isset($isDisplayArrow)) { $isDisplayArrow = true; }
    if (false == isset($isDisplayScaleIfSimulationNull)) { $isDisplayScaleIfSimulationNull = true; }

    if (false == $isDisplayScaleIfSimulationNull && null == $simulation) { return; };
    $isSimComplete = (null !== $simulation && $simulation->end !== null);
/* @var $simulation Simulation */
?>
<p>
    <?php if($isDisplayTitle): ?>
        <span class="skillstitle">Базовый менеджмент</span>
    <?php endif ?>
    <span <?php if (null !== $simulation) { echo 'data-simulation="/simulations/details/'.$simulation->id.'"';} ?>
        class="ratingwrap radiusthree <?php if(null!==$simulation && $simulation->end !== null) { echo "view-simulation-details-pop-up";} ?>">
        <span class="ratebg"><span class="rating" style="width: <?php if(null!==$simulation AND $simulation->end !== null AND $simulation->isFull()) { echo $simulation->getCategoryAssessment(); }else{ echo "0"; } ?>%"></span></span><sup>
            <?php if ($isSimComplete): ?>
                <?= $simulation->getCategoryAssessment() ?>%
            <?php else: ?>
                0%
            <?php endif ?>
        </sup></span>
    <?php if ($isDisplayArrow && $isSimComplete) : ?>
        <a href="#" data-simulation="/simulations/details/<?php echo $simulation->id; ?>" class="link-go view-simulation-details-pop-up"></a>
    <?php endif ?>
</p>