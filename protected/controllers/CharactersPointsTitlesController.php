<?php

include_once('protected/controllers/AjaxController.php');

/**
 * Description of CharactersPointsTitles
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersPointsTitlesController extends AjaxController{
    
    public function actionDraw()
    {
        $tableName = '';
        
        $search = Yii::app()->request->getParam('_search', false);              // true/false, в зависимости от этого идет выборка или не идет по
        $nd = Yii::app()->request->getParam('nd', false);
        $rows = Yii::app()->request->getParam('rows', false);                   // кол-во выбираемых записей
        $page = Yii::app()->request->getParam('page', false);                   // номер страницы для отображения(начало с 1) 
        $sidx = Yii::app()->request->getParam('sidx', false);                   // ключ для сортировки, ORDER BY $sidx
        $sord = Yii::app()->request->getParam('sord', false);                   // ASC/DESC
        $searchParams = Yii::app()->request->getParam('searchParams', false);   
        
        
        
        $limit = $page - 1;
        
        //$limit = 0; $rows =10;
        
        // Основаня выборка
        $sql = "select * from characters_points_titles ";
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

        $sql = "select count(*) as count from characters_points_titles";
        //echo $sql;
        $command = $connection->createCommand($sql);
        $row = $command->queryRow(); 
        
        $totalRows = $row['count'];
        
        $data = array(
            'result' => 1,
            'rows' => $records, //  записи грида
            'records' => $totalRows,   //  кол-во записей в таблице
            'page' => $page,
            'total' => ceil($totalRows / $rows)  // всего страниц
        );
        
	$this->_sendResponse(200, CJSON::encode($data));
    }
}

?>
