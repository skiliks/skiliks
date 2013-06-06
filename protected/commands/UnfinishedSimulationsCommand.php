<?php

class UnfinishedSimulationsCommand extends CConsoleCommand
{
    public function actionIndex($email = null)
    {
        $lite = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);
        $full = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $sql = 'end IS NULL AND (scenario_id = :lite AND start < :lite_start OR scenario_id = :full AND start < :full_start)';
        $params =  [
            'lite' => $lite->id,
            'full' => $full->id,
            'lite_start' => date('Y-m-d H:i:s', time() - 3600),
            'full_start' => date('Y-m-d H:i:s', time() - 3600 * 3)
        ];
        $columns = [
            'Invite ID'     => 4,
            'Simulation ID' => 4,
            'Scenario'      => 4,
            'Status'        => 9,
            'Start time'    => 19,
            'End time'      => 4
        ];
        $rowDrawer = function(Simulation $sim) {
            return [
                $sim->invite ? $sim->invite->id : '-',
                $sim->id,
                $sim->game_type->slug,
                $sim->invite ? $sim->invite->getStatusText() : '-',
                $sim->start,
                $sim->end ?: '-'
            ];
        };

        if (empty($lite) || empty($full)) {
            throw new LogicException('Scenarios do not exist');
        }

        if ($email) {
            $profile = YumProfile::model()->findByAttributes(['email' => $email]);
            if (empty($profile)) {
                throw new LogicException('User with this email does not exist');
            }

            $sql .= ' AND user_id = :userId';
            $params['userId'] = $profile->user_id;
        }

        $unfinished = Simulation::model()->findAll($sql, $params);

        echo ConsoleTools::table($columns, array_map($rowDrawer, $unfinished));
    }
}