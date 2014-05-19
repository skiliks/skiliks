<?php

class GenerateBehavioursCacheCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        SimulationService::generateBehavioursCache();
    }
}