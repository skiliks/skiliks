<section class="partial" xmlns="http://www.w3.org/1999/html">
    <label class="partial-label"><?= __FILE__ ?></label>
        <?php
            if (false == isset($isDisplayTitle)) { $isDisplayTitle = true; }
            if (false == isset($isDisplayArrow)) { $isDisplayArrow = true; }
            if (false == isset($isSkillsBox)   ) { $isSkillsBox = false; }
            if (false == isset($isDisplayScaleIfSimulationNull)) { $isDisplayScaleIfSimulationNull = true; }

            if (false == $isDisplayScaleIfSimulationNull && null == $simulation) { return; };

            $isSimComplete = (null !== $simulation && $simulation->end !== null && $simulation->isFull());
            $starsContainerCss = ($isSkillsBox) ? 'label background-middle-dark-blue' : '';
            $starsBoxCss = ($isSkillsBox) ? 'skills-box skills-box-stars' : 'skills-box-table';
            $percentileBoxCss = ($isSkillsBox) ? 'skills-box skills-box-percentile' : 'skills-box-table';
        /* @var $simulation Simulation */
        ?>

        <?php $user = Yii::app()->user->data(); ?>

        <?php if($user->profile->assessment_results_render_type == "standard") :?>
            <?php // stars ?>
            <p class="<?= $starsBoxCss ?>">
                <?php if ($isSimComplete): ?>
                    <?php if($isDisplayTitle): ?>
                        <label class="skills-title">Базовый менеджмент</label>
                    <?php endif ?>

                    <?php
                        $displayResultsClass = '';
                        // view-simulation-details-pop-up
                        if (null !== $simulation->end) { $displayResultsClass = 'action-display-simulation-details-pop-up'; }
                    ?>

                    <span data-simulation='/dashboard/simulationdetails/<?= $simulation->id ?>'
                        class="rating-stars-container <?= $starsContainerCss ?> <?= $displayResultsClass ?>">
                        <span class="rating-stars-indicator">
                            <span class="rating-stars-indicator-level" style="width: <?php echo $simulation->getCategoryAssessment(); ?>%">
                            </span>
                        </span>
                        <label class="ProximaNova-Bold"><?= $simulation->getCategoryAssessment() ?>%</label>
                    </span>

                    <?php if ($isDisplayArrow) : ?>
                        <!-- link-go view-simulation-details-pop-up -->
                        <span href="#"
                           data-simulation="/dashboard/simulationdetails/<?php echo $simulation->id; ?>"
                           class="action-display-simulation-details-popup icon-circle-with-blue-arrow icon-21x21-empty">
                        </span>
                    <?php endif ?>
                <?php endif; ?>
            </p>
        <?php else : ?>
            <?php if ($isSimComplete): ?>
                <p class="<?= $percentileBoxCss ?>" >
                    <?php if($isDisplayTitle): ?>
                        <label class="skills-title">Базовый менеджмент</label>
                    <?php endif ?>

                    <?php
                        $displayResultsClass = '';
                        // view-simulation-details-pop-up
                        if (null !== $simulation->end) { $displayResultsClass = 'action-display-simulation-details-pop-up'; }
                    ?>

                    <span data-simulation="/dashboard/simulationdetails/<?= $simulation->id ?>" class="button-white label rating-percentile-container <?= $displayResultsClass ?>">
                        <span class="rating-percentile-indicator">
                            <span class="rating-percentile-indicator-level" style="width:<?= round($simulation->invite->getPercentile()) ?>%;"></span>
                        </span>

                        <label class="ProximaNova-Bold">P<?=round($simulation->invite->getPercentile())  ?></label>
                    </span>

                    <?php if ($isDisplayArrow) : ?>
                        <span data-simulation="/dashboard/simulationdetails/<?php echo $simulation->id; ?>" class="action-display-simulation-details-popup icon-circle-with-blue-arrow icon-21x21-empty">
                            </span>
                    <?php endif ?>
                    </p>
            <?php endif; ?>
        <?php endif; ?>
</section>