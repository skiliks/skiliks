<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SimulationsDialogsPoints
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationsDialogsPoints extends CActiveRecord{
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'simulations_dialogs_points';
    }
    
    public function bySimulationAndPoint($simId, $pointId)
    {
        $simId = (int)$simId;
        $pointId = (int)$pointId;
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "sim_id = {$simId} and point_id = {$pointId}"
        ));
        return $this;
    }
}

?>
