<?php

include_once('protected/controllers/AjaxController.php');

/**
 * Description of CharactersPointsTitles
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersPointsTitlesController extends AjaxController{
    
    /**
     * Имя выбираемой таблицы
     * @var string
     */
    protected $_tableName = 'characters_points_titles';
    
    /**
     * Поля, по которым можно осуществлять фильтрацию
     * @var array
     */
    protected $_searchParams = array(
        'id', 'title'
    );
    
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
        $sql = "select * from {$this->_tableName} ";
        $whereSql = '';
        
        // фильтрация
        if ($search) {
            $where = array();
            foreach($this->_searchParams as $paramName) {
                $paramValue = Yii::app()->request->getParam($paramName, false); 
                if ($paramValue) {
                    $where[] =  $whereArr[]= $paramName.' LIKE "%'.$paramValue.'%"';
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
        $sql = "select count(*) as count from {$this->_tableName} ";
        $sql .= $whereSql;
        $command = $connection->createCommand($sql);
        $row = $command->queryRow(); 
        $totalRows = $row['count'];
        
        $data = array(
            'result' => 1,
            'rows' => $records,                 //  записи грида
            'records' => $totalRows,            //  кол-во записей в таблице
            'page' => $page,                    // номер страницы
            'total' => ceil($totalRows / $rows) // всего страниц
        );
        
	$this->_sendResponse(200, CJSON::encode($data));
    }
    
    /**
     * Обработчик изменения записи.
     * @return int
     */
    protected function _editHandler() {
        $id = (int)Yii::app()->request->getParam('id', false);
        $title = Yii::app()->request->getParam('title', false);

        $model = CharactersPointsTitles::model()->findByPk($id);
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

        $model = new CharactersPointsTitles();
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

        $model = CharactersPointsTitles::model()->findByPk($id);
        $model->delete();
        return 1;
    }
    
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
}

?>
