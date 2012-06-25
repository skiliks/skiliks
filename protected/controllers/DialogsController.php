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
    
    protected function _getComboboxHtml($tableName, $nameField = 'title') {
        $sql = "select * from {$tableName} ";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        
        $dataReader = $command->query();
        $html = '<select>';
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
                'characters' => $this->_getComboboxData('characters', 'name'),
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
        $this->_sendResponse(200, $this->_getComboboxHtml('characters', 'name'), 'text/html');
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
}

?>
