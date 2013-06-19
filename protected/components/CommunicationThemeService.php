<?php

class CommunicationThemeService {

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