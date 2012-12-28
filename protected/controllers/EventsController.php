<?php

/**
 * Движек событий
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsController extends AjaxController {

    /**
     * Опрос состояния событий
     */
    public function actionGetState() {
        
        $event = new Events();
        $json = $event->getState();
        //var_dump($json);
        $this->sendJSON($json);
        
    }
    
    
    
    /**
     * Возврат списка доступных событий в системе
     */
    public function actionGetList() {
        
        $event = new Events();
        $json = $event->getList();
        $this->sendJSON($json);
        
    }
    
    /**
     * Принудительный старт заданного события
     */
    public function actionStart() {
    
        $event = new Events();
        $json = $event->startEvent();
        $this->sendJSON($json);
    }
}
        

