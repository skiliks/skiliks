<?php
/**
 * Пересчитывает оценку у симуляции по email и ID симуляции
 */
class CalculateTheEstimateCommand extends CConsoleCommand {

    public function actionIndex($email, $simId)
    {
        SimulationService::CalculateTheEstimate($simId, $email);

        echo "done for email {$email} and simId {$simId}.\r\n";
    }

}