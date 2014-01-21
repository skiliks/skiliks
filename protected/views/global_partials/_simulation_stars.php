<section class="partial">
    <label class="partial-label"><?= __FILE__ ?></label>
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
                    <?php endif ?>

                    <!-- + { -->
                    <?php
                        $displayResultsClass = '';
                        // view-simulation-details-pop-up
                        if (null !== $simulation->end) { $displayResultsClass = 'action-display-simulation-details-pop-up'; }
                    ?>

                    <span data-simulation='/dashboard/simulationdetails/<?= $simulation->id ?>'
                        class="rating-stars-container <?= $displayResultsClass ?>">
                        <span class="rating-stars-indicator">
                            <span class="rating-stars-indicator-level" style="width: <?php echo $simulation->getCategoryAssessment(); ?>%">
                            </span>
                        </span>
                        <label class="ProximaNova-Bold"><?= $simulation->getCategoryAssessment() ?>%</label>
                    </span>
                    <!-- } + -->

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

                <!-- + { -->
                <?php
                    $displayResultsClass = '';
                // view-simulation-details-pop-up
                    if (null !== $simulation->end) { $displayResultsClass = 'action-display-simulation-details-pop-up'; }
                ?>
                <div data-simulation="/dashboard/simulationdetails/<?= $simulation->id ?>"
                    class="button-white label rating-percentile-container <?= $displayResultsClass ?>">
                    <span class="rating-percentile-indicator">
                        <span class="rating-percentile-indicator-level"
                            style="width:<?= round($simulation->invite->getPercentile()) ?>%;"></span>
                    </span>

                    <label class="ProximaNova-Bold">P<?=round($simulation->invite->getPercentile())  ?></label>
                </div>

                <!-- } + -->

                <?php if ($isDisplayArrow) : ?>
                    <a href="#" data-simulation="/dashboard/simulationdetails/<?php echo $simulation->id; ?>" class="link-go view-simulation-details-pop-up link-go-percentile"></a>
                    <?php if($isDisplayTitle): ?>
                        </p>
                    <?php endif ?>
                <?php endif ?>

            <?php endif; ?>
        <?php endif; ?>
</section>