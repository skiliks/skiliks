<?php

/**
 *
 *
 *
 */
class DebugController extends AjaxController
{
    public function actionIndex()
    {
        $simulation = Simulation::model()->findByPk(19);

        SimulationService::applyReductionFactors($simulation);
    }
}

