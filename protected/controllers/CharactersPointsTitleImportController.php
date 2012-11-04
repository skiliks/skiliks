<?php

/**
 * Импорт 
 *
 * @author Ivan Tugay <listepo@ya.ru>
 */
class CharactersPointsTitleImportController extends AjaxController {
    
    public function actionImport() {
    	
    	$start_time = microtime(true);
    	
    	$count_add_ceil = 0;
    	
    	$count_edit_ceil = 0;
    	
    	$count_add_title = 0;
    	
    	$count_edit_title = 0;
    	
    	$count_str = 0;
    	
    	$count_col = 0;
    	
    	$array_add_ceil = array();
    	
    	$array_edit_ceil = array();
    	
    	$array_add_title = array();
    	
    	$array_edit_title = array();
    	
    	$temp = array();
    	    	    	
    	$filename = './media/Forma1_new_170912_v3.xlsx';
    	
    	if( file_exists( $filename ) ) {
    		
        	$SimpleXLSX = new SimpleXLSX( $filename );
        	
        	$xlsx_data = $SimpleXLSX->rows();
        	
    	} else {
    		
    		throw new Exception( "Файл {$filename} не найден!" );
    		
    	}
    	
    	$transaction = Yii::app()->db->beginTransaction();
    	
    	try{
    		
	    	$db_parent = Yii::app()->db->createCommand()
	    		->select( 'id, parent_id, code, title, scale, type_scale' )
	    		->from( 'characters_points_titles' )
	    		->where( ' parent_id IS NULL ' )
	    		->queryAll();
	    	
	    	unset( $xlsx_data[0] );
	    	unset( $xlsx_data[count( $xlsx_data )] );
	    	
	    	$titles = array();
	    	$titles_ceil = array();
	    
	    	foreach ( $xlsx_data as $row ){
	    		    
	    			$pos = array_search( array( $row[1], $row[2] ), $titles_ceil );
	    			if( $pos == false ) {
	    				$titles_ceil[] = array( $row[1], $row[2] );
	    				$pos = array_search( array($row[1], $row[2] ), $titles_ceil );
	    				$titles[] = array ( $row[0], $pos, $row[3], $row[4], $row[5], $row[1] );
	    			} else {
	    				$titles[] = array( $row[0], $pos, $row[3], $row[4], $row[5], $row[1] );
	    			}
	    			
	    			    
	    		//$pos = array_search($needle, $haystack);
	    		//echo '<pre>';
	    		//var_dump($row);
	    		//echo '</pre>';
	    		
	    		
	    	}
	    	
	    	$count_str = count($xlsx_data);
	    	$count_col = count($xlsx_data[1]);
	    	
	    	unset($xlsx_data);
	    	
	    	$command = Yii::app()->db->createCommand();
	    	
	    	foreach ($titles_ceil as $k1 => $title) {
	    	
	    		$found = false;
	    		//Поиск совпаденией и обновление записей по коду
	    		foreach ($db_parent as $k2 => $data) {
	    			if($data['code'] == $title[0]){
	    				if($data['title'] == $title[1]){
	    						
	    				} else {
	    					//if($data['type_scale'] != $type_scale[$title[4]]){
	    					//	exit($data['type_scale']." - ".$type_scale[$title[4]]);
	    					//}
	    					//TODO:Изменить запись
	    					$command->update('characters_points_titles', array(
	    							'title'=>$title[1]
	    					), 'id=:id', array(':id'=>$data['id']));
	    					$array_edit_ceil[] = $title[0];
	    					$count_edit_ceil++;
	    				}
	    				$found = true;
	    				unset($titles_ceil[$k1]);
	    				unset($db_parent[$k2]);
	    	
	    			} else {
	    	
	    			}
	    			 
	    			//unset($title[$k2]);
	    		}
	    		if(!$found){
	    			//TODO:Добавить запись
	    			$command->insert('characters_points_titles', array(
	    					'code'=>$title[0],
	    					'title'=>$title[1]
	    			));
	    			//unset($db_data[$k1]);
	    			$count_add_ceil++;
	    			$array_add_ceil[] = $title[0];
	    		}
	    	}
	    	
	    	
	    	$db_data = Yii::app()->db->createCommand()
		    	->select('p2.id, p1.code as p_code, p2.code, p2.title, p2.scale, p2.type_scale')
		    	->from('characters_points_titles p1')
		    	->join('characters_points_titles p2', 'p1.id = p2.parent_id')
		    	->queryAll();
	    	
	    	$db_keys = Yii::app()->db->createCommand()
	    		->select('id, code')
	    		->from('characters_points_titles')
	    		->queryAll();
	
	    	$keys = array();
	    	foreach ($db_keys as $row)
	    	{
	    		$keys[$row['code']] = $row['id'];
	    	}
	    	//echo '<pre>';
	    	//var_dump($keys);
	    	//echo '</pre>';
	    	//exit();
	    	$type_scale = array('positive'=>'1', 'negative'=>'2', 'personal'=>'3');
	    	foreach ($titles as $k1 => $title) {
	    		
	    		$found = false;
	    		//Поиск совпаденией и обновление записей по коду
	    		foreach ($db_data as $k2 => $data) {
	    			if($data['code'] == $title[0]){
	    				if($data['title'] == $title[2] && $data['scale'] == $title[3] && $data['type_scale'] == $type_scale[$title[4]] && $data['p_code'] == $title[5]){
	    					
	    				} else {
	    					//if($data['type_scale'] != $type_scale[$title[4]]){
	    					//	exit($data['type_scale']." - ".$type_scale[$title[4]]);
	    					//}
	    					//TODO:Изменить запись
	    					$command->update('characters_points_titles', array(
	    							'parent_id'=>$keys[$title[5]],
	    							'title'=>$title[2],
	    							'scale'=>$title[3],
	    							'type_scale'=>$type_scale[$title[4]]
	    					), 'id=:id', array(':id'=>$data['id']));
	    					//echo $title[5].'<br>';
	    					$count_edit_title++;
	    					$array_edit_title[] = $title[0];
	    				}
	    				$found = true;
	    				unset($titles[$k1]);
	    				unset($db_data[$k2]);
	    				
	    			} else {
	    				
	    			}
	    			
	    			//unset($title[$k2]);
	    		}
	    		if(!$found){
		    		//TODO:Добавить запись
		    		$command->insert('characters_points_titles', array(
		    				'code'=>$title[0],
		    				'title'=>$title[2],
		    				'scale'=>$title[3],
		    				'type_scale'=>$title[4]
		    		));
		    		//unset($db_data[$k1]);
		    		$count_add_title++;
		    		$array_add_title[] = $title[0];
	    		}
	    	
	    	}
	    	
    		$transaction->commit();
    		
    	} catch ( Exception $e ) {
    		
    		$transaction->rollback();
    		
    		echo $e->getMessage()." в файле ".$e->getFile()." на строке  ".$e->getLine().'<br>';
    		exit("Обработаное исключение");
    	}
    	$end_time = microtime(true);
    	echo "<h3>";
    	echo "Файл - {$filename} <br>";
    	echo "Размер - ".(filesize($filename)/1024)." Кбайт <br>";
    	echo "Время последнего изменения файла  - ".date("d.m.Y H:i:s.", filemtime($filename))." <br>";
    	echo "Время импорта - ". ($end_time - $start_time).' c. <br>';
    	echo "Количество обработаных строк данных - ".$count_str." по ".$count_col.' колонки <br>';
    	echo "Обновлено {$count_edit_ceil} наименований целей обучения <br>";
    	if($array_edit_ceil != array()){
    		echo "Cреди них : ".implode(" , ", $array_edit_ceil)." <br>";
    	}
    	echo "Добавлено  {$count_add_ceil} наименованиий целей обучения <br>";
    	if($array_add_ceil != array()){
    		echo "Cреди них : ".implode(" , ", $array_add_ceil)." <br>";
    	}
    	echo "Обновлено {$count_edit_title} наименованиий требуемого поведения <br>";
    	if($array_edit_title != array()){
    		echo "Cреди них : ".implode(" , ", $array_edit_title)." <br>";
    	}
    	echo "Добавлено {$count_add_title} наименованиий требуемого поведения <br>";
    	if($array_add_title != array()){
    		echo "Cреди них : ".implode(" ,", $array_add_title)." <br>";
    	}
    	echo "Лишних наименований целей обучения в бд ".count($db_parent).'<br>';
    	if($db_parent != array()){
    		foreach ($db_parent as $t){
    			$temp[] = $t['code'];
    		}
    		echo "Cреди них : ".implode(" , ", $temp)." <br>";
    	}
    	echo "Лишних наименований требуемого поведения в бд ".count($db_data).'<br>';
    	$temp = array();
    	if($db_parent != array()){
    		foreach ($db_data as $t){
    			$temp[] = $t['code'];
    		}
    		echo "Cреди них : ".implode(" , ", $temp)." <br>";
    	}
    	echo "</h3>";
    //echo '<pre>';
    //var_dump($db_parent);
    //echo '</pre>';

    }
	public function actionLogdialog() {
		LogHelper::getDialogCSV();
	}
    
}