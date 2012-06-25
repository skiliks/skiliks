<?php

include_once('protected/controllers/DictionaryController.php');

/**
 * Контроллер диалогов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogsController extends DictionaryController{
    
    /**
     * 
     * dialog_types
dialog_subtypes
dialog_brenches
characters
characters_states
events_results
     */
    
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
    
    public function actionGetSelect() {
        $result = array(
            'result'=>1,
            'data'=>array(
                'dialog_types' => $this->_getComboboxData('dialog_types'),
                'dialog_subtypes' => $this->_getComboboxData('dialog_subtypes'),
                'dialog_branches' => $this->_getComboboxData('dialog_branches'),
                'characters' => $this->_getComboboxData('characters', 'name'),
                'characters_states' => $this->_getComboboxData('characters_states'),
                'events_results`' => $this->_getComboboxData('events_results`')
            )
        );
        
        $this->_sendResponse(200, CJSON::encode($result));
    }
    
    protected function _editHandler() {}
    
    protected function _addHandler() {}
    
    protected function _delHandler() {}
}

?>
