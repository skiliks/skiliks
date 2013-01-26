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
        $events = EventService::getEventsListForAdminka();
        
        $this->sendJSON(array(
            'result'  => 1,
            'rows'    => $events,
            'records' => count($events),
        ));
    } 
}


