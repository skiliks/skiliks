<?php

/**
 * Class GenerateSimulationResultTextCommand
 *
 * Текстовые комментарии для PDF с инфографикой
 */
class GenerateSimulationResultTextCommand extends CConsoleCommand
{
    public function actionIndex($from, $to)
    {
        SimulationResultTextService::generateForAllFullCompleteSimulations($from, $to);
    }
}