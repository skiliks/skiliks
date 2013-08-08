<?php
/**
 * Пересчитывает оценку у симуляции по email и ID симуляции
 */
class RestoreSCByLogCommand extends CConsoleCommand {

    public function actionIndex($simId, $code)
    {
        MyDocumentsService::restoreSCByLog($simId, $code);
        echo "Done \r\n";
    }

}