<?php



/**
 * Контроллер диалогов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogsController extends DictionaryController{
    
    protected $_searchParams = array(
        'id', 
        'ch_from',
        'ch_from_state',
        'ch_to',
        'ch_to_state',
        'dialog_subtype',
        'text',
        'duration',
        'event_result',
        'branch_id',
        'next_branch'
    );
    
    
    
    
    
    /**
     * Отдает информацию по всем комбикам
     */
    public function actionGetSelect() {
        $result = array(
            'result'=>1,
            'data'=>array(
                'dialog_types' => $this->_getComboboxData('dialog_types'),
                'dialog_subtypes' => $this->_getComboboxData('dialog_subtypes'),
                'dialog_branches' => $this->_getComboboxData('dialog_branches'),
                'characters' => $this->_getComboboxData('characters'),
                'characters_states' => $this->_getComboboxData('characters_states'),
                'events_results' => $this->_getComboboxData('events_results')
            )
        );
        
        $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionGetDialogTypesHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml('dialog_types'), 'text/html');
    }
    
    public function actionGetDialogSubtypesHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml('dialog_subtypes'), 'text/html');
    }
    
    public function actionGetDialogBranchesHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml('dialog_branches'), 'text/html');
    }
    
    public function actionGetCharactersHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml('characters'), 'text/html');
    }
    
    public function actionGetCharactersStatesHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml('characters_states'), 'text/html');
    }
    
    public function actionGetEventsResultsHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml('events_results'), 'text/html');
    }
    
    protected function _editHandler() {
        $id = (int)Yii::app()->request->getParam('id', false);
        
        $model = Dialogs::model()->findByPk($id);
        $model->ch_from = Yii::app()->request->getParam('ch_from', false);
        $model->ch_from_state = Yii::app()->request->getParam('ch_from_state', false);
        $model->ch_to = Yii::app()->request->getParam('ch_to', false);
        $model->ch_to_state = Yii::app()->request->getParam('ch_to_state', false);
        $model->dialog_subtype = Yii::app()->request->getParam('dialog_subtype', false);
        $model->text = Yii::app()->request->getParam('text', false);
        $model->duration = Yii::app()->request->getParam('duration', false);
        $model->event_result = Yii::app()->request->getParam('event_result', false);
        $model->branch_id = Yii::app()->request->getParam('branch_id', false);
        $model->next_branch = Yii::app()->request->getParam('next_branch', false);
        $model->save();
        return 1;
    }
    
    protected function _addHandler() {

        $model = new Dialogs();
        $model->ch_from = Yii::app()->request->getParam('ch_from', false);
        $model->ch_from_state = Yii::app()->request->getParam('ch_from_state', false);
        $model->ch_to = Yii::app()->request->getParam('ch_to', false);
        $model->ch_to_state = Yii::app()->request->getParam('ch_to_state', false);
        $model->dialog_subtype = Yii::app()->request->getParam('dialog_subtype', false);
        $model->text = Yii::app()->request->getParam('text', false);
        $model->duration = Yii::app()->request->getParam('duration', false);
        $model->event_result = Yii::app()->request->getParam('event_result', false);
        $model->branch_id = Yii::app()->request->getParam('branch_id', false);
        $model->next_branch = Yii::app()->request->getParam('next_branch', false);
        $model->insert();
        return 1;
    }
    
    /**
     * Обработчик удаление записи
     * @return int
     */
    protected function _delHandler() {
        $id = (int)Yii::app()->request->getParam('id', false);

        $model = Dialogs::model()->findByPk($id);
        $model->delete();
        return 1;
    }
    
    protected function _prepareSql() {
        return "select 
                    d.id,
                    cf.title as ch_from,
                    cfs.title as ch_from_state,
                    ct.title as ch_to,
                    cts.title as ch_to_state,
                    ds.title as dialog_subtype,
                    d.text,    
                    d.duration,
                    er.title as event_result,
                    db.title as branch_id,
                    dnb.title as next_branch

                from dialogs as d
                left join dialog_branches as db on (db.id = d.branch_id)
                left join characters as cf on (cf.id = d.ch_from)
                left join characters_states as cfs on (cfs.id = d.ch_from_state)
                
                left join characters as ct on (ct.id = d.ch_to)
                left join characters_states as cts on (cts.id = d.ch_to_state)
                
                left join dialog_subtypes as ds on (ds.id = d.dialog_subtype)
                left join events_results as er on (er.id = d.event_result)
                left join dialog_branches as dnb on (dnb.id = d.next_branch)
        ";
    }
    
    protected function _prepareCountSql() {
        return "select 
                    count(*) as count

                from dialogs as d
                left join dialog_branches as db on (db.id = d.branch_id)
                left join characters as cf on (cf.id = d.ch_from)
                left join characters_states as cfs on (cfs.id = d.ch_from_state)
                
                left join characters as ct on (ct.id = d.ch_to)
                left join characters_states as cts on (cts.id = d.ch_to_state)
                
                left join dialog_subtypes as ds on (ds.id = d.dialog_subtype)
                left join events_results as er on (er.id = d.event_result)
                left join dialog_branches as dnb on (db.id = d.next_branch)
        ";
    }
    
    public function actionGetPoints() {
        $id = (int)Yii::app()->request->getParam('id', false);
        
        $data = array();
        
        $items = CharactersPointsTitles::model()->withoutParents()->findAll();
        foreach($items as $item) {
            $data['parents'][] = array(
                'id' => $item->id,
                'name' => $item->title,
                'code' => $item->code
            );
        }
        /*
        $items = CharactersPointsTitles::model()->withParents()->findAll();
        foreach($items as $item) {
            $data['childs'][] = array(
                'id' => $item->id,
                'parent_id' => $item->parent_id,
                'code' => $item->code,
                'name' => $item->title,
                
            );
        }
        */
        
        
        $sql = "select 
                    cp.id,
                    cp.point_id,    
                    cpt.title,
                    cp.add_value,
                    cpt.parent_id,
                    cpt.code
                from characters_points as cp
                left join characters_points_titles as cpt on (cpt.id = cp.point_id)
                where cp.dialog_id={$id}  and cpt.parent_id > 0";
                
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);       
        $dataReader = $command->query();
        $values = array();
        foreach($dataReader as $row) { 
            $values[$row['point_id']] = $row['add_value'];
        }
        //var_dump($values);
        $sql = "select * from characters_points_titles where parent_id > 0";
        $command = $connection->createCommand($sql);       
        $dataReader = $command->query();
        
        foreach($dataReader as $row) { 
            $item = array();
            $item['id'] = $row['id'];
            $item['parent_id'] = $row['parent_id'];
            $item['code'] = $row['code'];
            $item['name'] = $row['title'];
            if (isset($values[$row['id']])) {
                $item['flag'] = 1;
                $item['add_value'] = $values[$row['id']];
            }
            else {
                $item['flag'] = 0;
                $item['add_value'] = '';
            }
            $data['childs'][] = $item;
        }
        
        $data = array(
            'result' => 1,
            'data' => $data
        );
        
	$this->_sendResponse(200, CJSON::encode($data));
    }
    
    public function actionSavePoints() {
        $dialogId = (int)Yii::app()->request->getParam('id', false);
        $points = Yii::app()->request->getParam('points', false);
        
        $sql = "delete from characters_points where dialog_id={$dialogId}";
        
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);       
        $command->execute();
        
        foreach($points as $pointId => $data) {
            if ($data['flag'] == 1) {
                $model = new CharactersPoints();
                $model->dialog_id = $dialogId;
                $model->point_id = $pointId;
                $model->add_value = $data['value'];
                $model->insert();
            }
        }
        
        $data = array(
            'result' => 1
        );
        
	$this->_sendResponse(200, CJSON::encode($data));
    }
}

?>
