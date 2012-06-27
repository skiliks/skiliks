<?php

include_once('protected/controllers/DictionaryController.php');

/**
 * Description of EventsResults
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsResultsController  extends DictionaryController{
    
    /**
     * Имя выбираемой таблицы
     * @var string
     */
    protected $_tableName = 'events_results';
    
    /**
     * Поля, по которым можно осуществлять фильтрацию
     * @var array
     */
    protected $_searchParams = array(
        'id', 'title'
    );
    
    
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
