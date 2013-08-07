<?php
/**
 * Удаляет все файлы которые не являются сводным бюджетом
 * Class DeleteNotD1Command
 */
class DeleteSimulationsForUserCommand extends CConsoleCommand {

    public function actionIndex($email, $justCheck = false)
    {
        echo "Начинаем удалять: \n";

        if ($justCheck) {
            echo "Только проверка! \n";
        }

        $profile = YumProfile::model()->findByAttributes(['email' => $email]);

        if (null === $profile) {
            echo 'Пользователь не найден!';
            return;
        }

        $simulations = Simulation::model()->findAllByAttributes(['user_id' => $profile->user_id]);

         // @var Simulation $sim
        foreach ($simulations as $simulation) {

            echo 'удаляю ' . $simulation->id . "\n";

            if (false === $justCheck) {
                SimulationService::removeSimulationData(
                    YumUser::model()->findByAttribute(['email' => $email]),
                    $simulation
                );
            }
        }

        echo "Операции завершены! \n";
    }
}