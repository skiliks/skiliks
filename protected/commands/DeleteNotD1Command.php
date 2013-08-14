<?php
/**
 * Удаляет все файлы которые не являются сводным бюджетом
 * Class DeleteNotD1Command
 */
class DeleteNotD1Command extends CConsoleCommand {

    public function actionIndex()
    {
        echo "Начинаем удалять ексель файлы кроме  D1: \n";

        $sims = Simulation::model()->findAll();

         // @var Simulation $sim
        foreach ($sims as $simulation) {
            echo ".";

            $docs = MyDocument::model()->findAllByAttributes([
                'sim_id' => $simulation->id
            ]);
            foreach ($docs as $document) {
                if ('D1' !== $document->template->code && file_exists($document->getFilePath())) {
                    unlink($document->getFilePath());
                    echo "Симуляция {$simulation->id}, {$document->fileName} удалён. \n";
                }
            }
        }

        echo "Готово! \n";
    }
}