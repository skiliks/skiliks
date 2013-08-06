<?php
/**
 * Удаляет все файлы которые не являются сводным бюджетом
 * Class DeleteNotD1Command
 */
class DeleteDataForDeletedSimulationsCommand extends CConsoleCommand {

    public function actionIndex($email, $justCheck = false)
    {
        echo "Начинаем удалять: \n";

        if ($justCheck) {
            echo "Только проверка! \n";
        }

        $emails = MailBox::model()->findAll(['group' => 'sim_id']);

         // @var Simulation $sim
        foreach ($emails as $email) {
            echo '.';

            $simulation = Simulation::model()->findByPk($email->sim_id);
            if (null === $simulation) {
                if (false === $justCheck) {
                    echo "\n удаляю {$email->sim_id} ";
                    SimulationService::removeSimulationData(
                        YumUser::model()->findByAttribute(['email' => $email]),
                        null,
                        $email->sim_id
                    );
                    echo "\n ...готово.\n";
                } else {
                    echo "\n {$email->sim_id} ";
                }
            }
        }

        echo "Операции завершены! \n";
    }
}