<?php
/**
 * Удаляет все файлы которые не являются сводным бюджетом
 * Class DeleteNotD1Command
 */
class MakeStartedSimulationsCompleteCommand extends CConsoleCommand {

    public function actionIndex()
    {
        echo "Start: \n";

        $startedInvites = Invite::model()->findAllByAttributes([
            'status' => Invite::STATUS_STARTED
        ]);

         // @var Simulation $sim
        foreach ($startedInvites as $invite) {
            echo sprintf(
                'Invite: email %s, type: %s, invite id %s, sim id: %s '."\n",
                @$invite->receiverUser->profile->email,
                @$invite->simulation->game_type->slug,
                @$invite->id,
                @$invite->simulation->id
            );

            $invite->status = Invite::STATUS_COMPLETED;
            $invite->save(false);
        }

        $startedSimulations = Simulation::model()->findAll('start IS NOT NULL AND end IS NULL');

        // @var Simulation $sim
        foreach ($startedSimulations as $simulation) {
            echo sprintf(
                'Simulations: email %s, type: %s, sim id: %s '."\n",
                @$simulation->user->profile->email,
                @$simulation->game_type->slug,
                @$simulation->id
            );

            $simulation->end = '0001-01-01 01:01:01';
            $simulation->save(false);
        }

        echo "Done! \n";
    }
}