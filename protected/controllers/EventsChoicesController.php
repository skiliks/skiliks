<?php

include_once('protected/controllers/DictionaryController.php');

/**
 * Справочник взаимосвязи событий
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsChoicesController extends DictionaryController{
    
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
        
        $this->_sendResponse(200, CJSON::encode($result));
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
        $title = Yii::app()->request->getParam('title', false);

        $model = EventsResults::model()->findByPk($id);
        $model->title = $title;
        $model->save();
        return 1;
    }
    
    /**
     * Обработчик на добавление записи.
     * @return int
     */
    protected function _addHandler() {
        $title = Yii::app()->request->getParam('title', false);

        $model = new EventsResults();
        $model->title = $title;
        $model->insert();
        return 1;
    }
    
    /**
     * Обработчик удаление записи
     * @return int
     */
    protected function _delHandler() {
        $id = (int)Yii::app()->request->getParam('id', false);

        $model = EventsResults::model()->findByPk($id);
        $model->delete();
        return 1;
    }
}

?>
