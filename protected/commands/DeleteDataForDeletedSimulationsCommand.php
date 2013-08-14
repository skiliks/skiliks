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

        // we does`n use group by
        // @link: http://blog.mclaughlinsoftware.com/2010/03/10/mysql-standard-group-by/
        // ERROR 1055 (42000): 'mail_box.id' isn't in GROUP BY
        $emails = MailBox::model()->findAll();
        // $emails = AssessmentPoint::model()->findAll();
        
        $sims = [];

         // @var Simulation $sim
        foreach ($emails as $email) {
            echo '.';

            if (isset($sims)) {
                continue;
            }

            $sims[$email->sim_id] = true;

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