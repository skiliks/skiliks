<?php

class SetReplicaDurationCommand extends CConsoleCommand {

    public function actionIndex($duration, $scenario_type)
    {
        $scenario = Scenario::model()->findByAttributes(['slug'=>$scenario_type]);
        $replicas = Replica::model()->findAllByAttributes(['scenario_id'=>$scenario->id]);
        /* @var $replica Replica */
        foreach($replicas as $replica) {
            if((int)$replica->from_character->code !== Character::HERO_ID) {
                var_dump($replica->from_character->code);
                $replica->duration = $duration;
                $replica->update();
            }
        }
        echo "Done!\r\n";
    }

}