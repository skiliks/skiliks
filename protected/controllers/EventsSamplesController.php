<?php



/**
 * Description of EventsSamplesController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsSamplesController extends DictionaryController{
    
    protected $_searchParams = array(
        'id', 
        array(
            'paramName' => 'title',
            'fieldName' => 'es.title'
        ), 
        /*array(
            'paramName' => 'dialog_id',
            'fieldName' => 'es.dialog_id'
        ), */
        'on_ignore_result', 
        'on_hold_logic',
        'code'
    );
    
    
    /**
     * Отдает информацию по всем комбикам
     */
    public function actionGetSelect() {
        $result = array(
            'result'=>1,
            'data'=>array(
                //'dialogs' => $this->_getComboboxData('dialogs', 'text', ' where step_number=1 and replica_number=0 '),
                'events_results' => $this->_getComboboxData('events_results'),
                'events_on_hold_logic' => $this->_getComboboxData('events_on_hold_logic')
            )
        );
        
        $this->sendJSON($result);
    }
    
    public function actionGetDialogsHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml('dialogs', 'text', ' where step_number=1 and replica_number=0 '), 'text/html');
    }
    
    public function actionGetEventsResultsHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml('events_results'), 'text/html');
    }
    
    public function actionGetEventsOnHoldLogicHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml('events_on_hold_logic'), 'text/html');
    }
    
    protected function _editHandler() {
        $id = (int)Yii::app()->request->getParam('id', false);
        
        $model = EventsSamples::model()->findByPk($id);
        $model->title = Yii::app()->request->getParam('title', false);
        $model->on_ignore_result = (int)Yii::app()->request->getParam('on_ignore_result', false);
        $model->on_hold_logic = (int)Yii::app()->request->getParam('on_hold_logic', false);
        $model->code = Yii::app()->request->getParam('code', false);
        $model->save();
        return 1;
    }
    
    protected function _addHandler() {
        $model = new EventsSamples();
        $model->title = Yii::app()->request->getParam('title', false);
        $model->on_ignore_result = (int)Yii::app()->request->getParam('on_ignore_result', false);
        $model->on_hold_logic = (int)Yii::app()->request->getParam('on_hold_logic', false);
        $model->code = Yii::app()->request->getParam('code', false);
        $model->insert();
        return 1;
    }
    
    protected function _delHandler() {
        $id = (int)Yii::app()->request->getParam('id', false);

        $model = EventsSamples::model()->findByPk($id);
        $model->delete();
        return 1;
    }
    
    protected function _prepareSql() {
        return "select 
                    es.id,
                    es.code,
                    es.title,
                    er.title as on_ignore_result,
                    ehl.title as on_hold_logic
                from events_samples as es
                left join events_on_hold_logic as ehl on (ehl.id = es.on_hold_logic)
                left join events_results as er on (er.id = es.on_ignore_result)
        ";
    }
    
    protected function _prepareCountSql() {
        return "select 
                    count(*) as count

                from events_samples as es
                left join events_on_hold_logic as ehl on (ehl.id = es.on_hold_logic)
                left join events_results as er on (er.id = es.on_ignore_result)
        ";
    }
}

?>
