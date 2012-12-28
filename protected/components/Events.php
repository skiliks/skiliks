<?php

class Events {
    
    protected $sid;
    
    protected $eventCode;
    
    protected $delay;
    
    protected $clearEvents;
    
    protected $clearAssessment;
    
    protected $simId;
    
    protected $event;
    
    protected $gameTime;

    public function __construct() {
        
        $this->initEventParams();
        
    }
    
    protected function initEventParams() {
        
        $this->sid = Yii::app()->request->getParam('sid', false);  
        $this->eventCode = Yii::app()->request->getParam('eventCode', false);  
        $this->delay = (int)Yii::app()->request->getParam('delay', false);  
        $this->clearEvents = Yii::app()->request->getParam('clearEvents', false);  
        $this->clearAssessment = Yii::app()->request->getParam('clearAssessment', false);
        
    }

    public function startEvent() {
        
        try {
            if (!$this->sid) throw new Exception('Не задан сид');
            
            $this->uid = SessionHelper::getUidBySid();
            if (!$this->uid) throw new Exception('Не могу определить пользователя');
            
            $this->simId = SessionHelper::getSimIdBySid($this->sid);
                        
            $this->event = EventsSamples::model()->byCode($this->eventCode)->find();
            if (!$this->event) throw new Exception('Не могу определить событие по коду : '.  $this->eventCode);
            
            // если надо очищаем очерель событий для текущей симуляции
            if ($this->clearEvents) {
                EventsTriggers::model()->deleteAll("sim_id={$this->simId}");
            }
            
            // если надо очищаем оценки  для текущей симуляции
            if ($this->clearAssessment) {
                SimulationsDialogsPoints::model()->deleteAll("sim_id={$this->simId}");
            }
            
            $this->gameTime = SimulationService::getGameTime($this->simId);
            $this->gameTime = $this->gameTime + $this->delay;  //time() + ($delay/4);
            
            
            $eventsTriggers = EventsTriggers::model()->bySimIdAndEventId($this->simId, $this->event->id)->find();
            if ($eventsTriggers) {
                $eventsTriggers->trigger_time = $this->gameTime;
                $eventsTriggers->save(); // обновляем существующее событие в очереди
            }
            else {
                
                // Добавляем событие
                $eventsTriggers = new EventsTriggers();
                $eventsTriggers->sim_id = $this->simId;
                $eventsTriggers->event_id = $this->event->id;
                $eventsTriggers->trigger_time = $this->gameTime;
                $eventsTriggers->insert();
            }
            
            return ['result' => 1];
            
        } catch (Exception $exc) {
            return [
                'result' => 0, 
                'message' => $exc->getMessage()
            ];
        }
        
    }
    
}
