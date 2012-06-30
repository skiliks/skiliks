<?php



/**
 * Контроллер симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationController extends AjaxController{
    
    /**
     * Старт симуляции
     */
    public function actionStart() {
        $sid = (int)Yii::app()->request->getParam('sid', false);
        
        $uid = SessionHelper::getUidBySid($sid);
        if (!$uid) {
            $result = array('result' => 0, 'message' => 'cant find user');
            return $this->_sendResponse(200, CJSON::encode($result));
        }
        
        // Удаляем предыдущую симуляцию
        $simulation = Simulations::model()->findByAttributes(array('user_id'=>$uid));
        if ($simulation) $simulation->delete();
        
        // Создаем новую симуляцию
        $simulation = new Simulations();
        $simulation->user_id = $uid;
        $simulation->status = 1;
        $simulation->start = time();
        $simulation->difficulty = 1;
        $simulation->insert();
        
        $simId = $simulation->id;
        
        // Сделать вставки в events triggers
        $events = EventsSamples::model()->limit(1)->findAll();
        foreach($events as $event) {
            $eventsTriggers = new EventsTriggers();
            $eventsTriggers->sim_id = $simId;
            $eventsTriggers->event_id = $event->id;
            $eventsTriggers->trigger_time = time() + 20; //rand(1*60, 5*60);
            $eventsTriggers->save();
        }
        
        $result = array('result' => 1);
        $this->_sendResponse(200, CJSON::encode($result));
    }
    
    /**
     * Остановка симуляции
     */
    public function actionStop() {
        $uid = (int)Yii::app()->request->getParam('uid', false);
        
        $model = Simulations::model()->findByAttributes(array('user_id'=>$uid));
        if ($model) {
            $model->end = time();
            $model->status = 0;
            $model->save();
        }
        
        $result = array('result' => 1);
        $this->_sendResponse(200, CJSON::encode($result));
    }
}

?>
