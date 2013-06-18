<?php

class CommunicationThemeService {

    public function addToTheLogUsed(Simulation $simulation, $theme_id) {
        $log = new LogCommunicationThemeUsage();
        $log->sim_id = $simulation->id;
        $log->communication_theme_id = $theme_id;
        if(false === $log->save(false)){
            throw new Exception("Not save");
        }
    }

}