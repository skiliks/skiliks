<?php
/**
 * Удаляет все файлы которые не являются сводным бюджетом
 * Class DeleteNotD1Command
 */
class DeleteSimulationsForUserCommand extends CConsoleCommand {

    public function actionIndex($email, $justCheck = '0', $simId = null)
    {
        echo "Начинаем удалять: \n";

        if ('0' != $justCheck) {
            echo "Только проверка. \n";
        }

        $profile = YumProfile::model()->findByAttributes(['email' => strtolower($email)]);

        if (null === $profile) {
            echo 'Пользователь не найден.';
            return;
        }

        $simulations = [];

        if (null == $simId) {
            $simulations = Simulation::model()->findAllByAttributes(['user_id' => $profile->user_id]);
        } else {
            $simulation = Simulation::model()->findByAttributes(['id' => $simId]);
            if (null !== $simulation) {
                $simulations = [$simulation];
            }
        }

        if (0 == count($simulations)) {
            echo "Симуляции не найдены. \n";
        }

         // @var Simulation $sim
        foreach ($simulations as $simulation) {

            if ('0' != $justCheck) {
                echo $simulation->id . ", ";
            } else {
                echo 'удаляю ' . $simulation->id . "\n";
                SimulationService::removeSimulationData(
                    YumProfile::model()->findByAttributes(['email' => strtolower($email)])->user,
                    $simulation
                );
            }
        }

        echo "\nОперации завершены. \n";
    }
}