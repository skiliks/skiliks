<?php

class GenerateSimulationResultTextCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        SimulationResultTextService::generateForAllFullCompleteSimulations();
    }
}