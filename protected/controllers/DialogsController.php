<?php

include_once('protected/controllers/DictionaryController.php');

/**
 * Контроллер диалогов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogsController extends DictionaryController{
    
    
    
    protected function _getComboboxData($tableName, $nameField = 'title') {
        $sql = "select * from {$tableName} ";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        
        $dataReader = $command->query();
        $data = array();
        foreach($dataReader as $row) { 
            $records[] = $row['id'].':'.$row[$nameField];
        }
        
        return implode(';', $records);
    }
    
    /**
     * Отображение комбика в виде html
     * @param type $tableName
     * @param type $nameField
     * @return string 
     */
    protected function _getComboboxHtml($tableName, $nameField = 'title') {
        $sql = "select * from {$tableName} ";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        
        $dataReader = $command->query();
        $html = '<select>';
        $html .= "<option value='-1'>Все</option>";
        foreach($dataReader as $row) { 
            $html .= "<option value='{$row['id']}'>{$row[$nameField]}</option>";
        }
        $html .= '</select>';
        
        return $html;
    }
    
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
    
    protected function _editHandler() {}
    
    protected function _addHandler() {}
    
    protected function _delHandler() {}
    
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
                left join dialog_branches as dnb on (db.id = d.next_branch)
        ";
    }
}

?>
