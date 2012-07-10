<?php



/**
 * Контроллер для построение справочников
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
abstract class DictionaryController extends AjaxController{
    
    /**
     * Имя выбираемой таблицы
     * @var string
     */
    protected $_tableName = false;
    
    /**
     * Поля, по которым можно осуществлять фильтрацию
     * @var array
     */
    protected $_searchParams = array();
    
    /**
     * Подготовка запроса на выборку записей для грида
     * @return string
     */
    protected function _prepareSql() {
        return "select * from {$this->_tableName} ";
    }
    
    protected function _prepareCountSql() {
        return "select count(*) as count from {$this->_tableName} ";
    }
    
    /**
     * Формирование данных для отдачи гриду
     * 
     * @return array
     * <code>
     *  array(
     *      'result' => 1,
     *      'rows' =>  array (записи грида
     *          'id' => int
     *          'cell' => array of fetched assoc array
     *      )
     *      'records' =>  int кол-во записей в таблице
     *      'page' => int номер страницы
     *      'total' => int всего страниц
     *  )
     * </code>
     */
    public function actionDraw()
    {
        $search = Yii::app()->request->getParam('_search', false);              // true/false, в зависимости от этого идет выборка или не идет по
        $nd = Yii::app()->request->getParam('nd', false);
        $rows = (int)Yii::app()->request->getParam('rows', false);              // кол-во выбираемых записей
        $page = (int)Yii::app()->request->getParam('page', false);              // номер страницы для отображения(начало с 1) 
        $sidx = Yii::app()->request->getParam('sidx', false);                   // ключ для сортировки, ORDER BY $sidx
        $sord = Yii::app()->request->getParam('sord', false);                   // ASC/DESC
        
        
        $limit = $page - 1;
        

        
        // Основаня выборка
        $sql = $this->_prepareSql();
        $whereSql = '';
        
        // фильтрация
        if ($search) {
            $where = array();
            foreach($this->_searchParams as $paramName) {
                $fieldName = $paramName;
                
                // Если переданы дополнительные параметры
                if (is_array($paramName)) {
                    $params = $paramName;
                    $paramName = $params['paramName'];
                    $fieldName = $params['fieldName'];
                }
                
                $paramValue = Yii::app()->request->getParam($paramName, false); 
                if ($paramValue == -1) continue;
                
                if ($paramValue) {
                    $where[] = $fieldName.' LIKE "%'.$paramValue.'%"';
                }
            }
            if (count($where)>0) {
                $whereSql = ' where '.implode(" AND ", $where);
            }
        }
        $sql .= $whereSql;
        
        if ($sidx) {
            $sql .= " order by $sidx $sord ";
        }
        $offset = $limit*$rows;
        $sql .= " limit {$rows} offset {$offset}";
        
        Logger::debug("dictionary sql : $sql");
        
        //echo($sql);
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        
        $dataReader = $command->query();
        $records = array();
        foreach($dataReader as $row) { 
            $cell = array();
            foreach($row as $f) {
                $cell[] = $f;
            }
            
            $records[] = array(
                'id' => $row['id'],
                'cell' => $cell
            );
        }

        // получение общего колличества записей в рамках критерий фильтрации
        $sql = $this->_prepareCountSql();
        $sql .= $whereSql;
        $command = $connection->createCommand($sql);
        $row = $command->queryRow(); 
        $totalRows = $row['count'];
        
        $total = 0;
        if ($rows > 0) $total = ceil($totalRows / $rows);
        
        $data = array(
            'result' => 1,
            'rows' => $records,                 //  записи грида
            'records' => $totalRows,            //  кол-во записей в таблице
            'page' => $page,                    // номер страницы
            'total' => $total                   // всего страниц
        );
        
	$this->_sendResponse(200, CJSON::encode($data));
    }
    
    abstract protected function _editHandler();
    
    abstract protected function _addHandler();
    
    abstract protected function _delHandler();
    
    public function actionEdit()
    {
        $oper = Yii::app()->request->getParam('oper', false);
        
        $result = 0;
        if ($oper == 'edit') {
            $result = $this->_editHandler();
        }
        
        if ($oper == 'add') {
            $result = $this->_addHandler();
        }
        
        if ($oper == 'del') {
            $result = $this->_delHandler();
        }

        $this->_sendResponse(200, CJSON::encode(array('result' => $result)));
    }
    
    protected function _getComboboxData($tableName, $nameField = 'title', $condition='', $firstValue = false) {
        $sql = "select * from {$tableName} ".$condition;
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        
        $dataReader = $command->query();
        $records = array();
        if ($firstValue) {
            $records[] = '-1:'.$firstValue;
        }
        
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
    protected function _getComboboxHtml($tableName, $nameField = 'title', $condition='', $idField = 'id') {
        $sql = "select * from {$tableName} ".$condition;
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        
        $dataReader = $command->query();
        $html = '<select>';
        $html .= "<option value='-1'>Все</option>";
        foreach($dataReader as $row) { 
            $html .= "<option value='{$row[$idField]}'>{$row[$nameField]}</option>";
        }
        $html .= '</select>';
        
        return $html;
    }
}

?>
