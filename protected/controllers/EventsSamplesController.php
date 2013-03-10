<?php
/**
 * Adminka
 * Description of EventsSamplesController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsSamplesController extends AjaxController
{
    /**
     * Отдает информацию по всем комбикам - WTF?
     * Used in adminka only
     * 
     * We need it just because, currently adminka steel send request
     */
    public function actionGetSelect() 
    {
        $this->sendJSON(array(
            'result'=>1,
            'data'=>array()   
        ));
    }
    
    /**
     * We need it just because, currently adminka steel send request
     */
    public function actionGetEventsResultsHtml() { }
    
    /**
     * We need it just because, currently adminka steel send request
     */
    public function actionGetEventsOnHoldLogicHtml() { }
    
    /**
     * Return data for "Примеры событий" table in adminka
     */
    public function actionDraw()
    {
        $events = array();

        $codes = array();
        foreach (EventSample::model()->findAll() as $event) {
            if (false === in_array($event->code, $codes)) {
                $codes[] = $event->code;
                $events[] = array(
                    'id'    => $event->id,
                    'cell'  => array(
                        $event->id,
                        $event->code,
                        $event->title,
                        (7 == $event->on_ignore_result) ? "нет результата" : $event->on_ignore_result,
                        (1 == $event->on_hold_logic) ? "ничего" : $event->on_hold_logic  // current import set this value
                    )
                );
            }
        }
        
        $this->sendJSON(array(
            'result'  => 1,
            'rows'    => $events,
            'records' => count($events),
        ));
    } 
}


