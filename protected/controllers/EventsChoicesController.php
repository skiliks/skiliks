<?php



/**
 * Справочник взаимосвязи событий
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsChoicesController extends DictionaryController{
    
    protected $_searchParams = array(
        'id', 'event_id', 'event_result', 'delay', 'dstEventId'
    ); 
    
    
    /**
     * Отдает информацию по всем комбикам
     */
    public function actionGetSelect() {
        $result = array(
            'result'=>1,
            'data'=>array(
                'events_samples' => $this->_getComboboxData('events_samples'),
                'events_results' => $this->_getComboboxData('events_results')
            )
        );
        
        $this->sendJSON($result);
    }
    
    public function actionGetEventsSamplesHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml('events_samples'), 'text/html');
    }
    
    public function actionGetEventsResultsHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml('events_results'), 'text/html');
    }
    
    /**
     * Обработчик изменения записи.
     * @return int
     */
    protected function _editHandler() {
        $id = (int)Yii::app()->request->getParam('id', false);
        $event_id = Yii::app()->request->getParam('event_id', false);
        $event_result = Yii::app()->request->getParam('event_result', false);
        $delay = Yii::app()->request->getParam('delay', false);
        $dstEventId = Yii::app()->request->getParam('dstEventId', false);


        $model = EventsChoices::model()->findByPk($id);
        $model->event_id = $event_id;
        $model->event_result = $event_result;
        $model->delay = $delay;
        $model->dstEventId = $dstEventId;
        $model->save();
        return 1;
    }
    
    /**
     * Обработчик на добавление записи.
     * @return int
     */
    protected function _addHandler() {
        $event_id = Yii::app()->request->getParam('event_id', false);
        $event_result = Yii::app()->request->getParam('event_result', false);
        $delay = Yii::app()->request->getParam('delay', false);
        $dstEventId = Yii::app()->request->getParam('dstEventId', false);

        $model = new EventsChoices();
        $model->event_id = $event_id;
        $model->event_result = $event_result;
        $model->delay = $delay;
        $model->dstEventId = $dstEventId;
        $model->insert();
        return 1;
    }
    
    /**
     * Обработчик удаление записи
     * @return int
     */
    protected function _delHandler() {
        $id = (int)Yii::app()->request->getParam('id', false);

        $model = EventsChoices::model()->findByPk($id);
        $model->delete();
        return 1;
    }
    
    protected function _prepareSql() {
        return "select 
                    ec.id,
                    es2.title as event_id,
                    er.title as event_result,
                    ec.delay,
                    es.title as dstEventId
                from events_choices as ec
                left join events_samples as es on (es.id = ec.dstEventId)
                left join events_samples as es2 on (es2.id = ec.event_id)
                left join events_results as er on (er.id = ec.event_result)
        ";
    }
    
    protected function _prepareCountSql() {
        return "select 
                    count(*) as count

                from events_choices as ec
                left join events_samples as es on (es.id = ec.dstEventId)
                left join events_samples as es2 on (es2.id = ec.event_id)
                left join events_results as er on (er.id = ec.event_result)
        ";
    }
}

?>
