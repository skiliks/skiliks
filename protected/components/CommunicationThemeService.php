<?php

/**
 * Class CommunicationThemeService
 */
class CommunicationThemeService {

    /**
     * Отмечает что такая тема уже была использована
     * @param Simulation $simulation
     * @param $theme_id int communication_theme_id в LogCommunicationThemeUsage
     * @throws Exception
     */
    public function addToTheLogUsed(Simulation $simulation, $theme_id) {

        $log = LogCommunicationThemeUsage::model()->findByAttributes(['sim_id'=>$simulation->id, 'communication_theme_id'=>$theme_id]);
        if(null === $log){
            $log = new LogCommunicationThemeUsage();
            $log->sim_id = $simulation->id;
            $log->communication_theme_id = $theme_id;
            if(false === $log->save(false)){
                throw new Exception("Not save");
            }
        }

    }

}