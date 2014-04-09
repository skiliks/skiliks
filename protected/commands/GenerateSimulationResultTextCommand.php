<?php

/**
 * Class GenerateSimulationResultTextCommand
 *
 * Текстовые комментарии для PDF с инфографикой
 */
class GenerateSimulationResultTextCommand extends CConsoleCommand
{
    public function actionIndex()
    {
        SimulationResultTextService::generateForAllFullCompleteSimulations();
    }
}