<?php

class LogHelper {
	
	public function __construct() {
		
	}
	
	public static function getLogDoialog($dialogId, $simId, $pointId) {
		
		$comand = Yii::app()->db->createCommand();
		$comand->insert("log_dialog", array(
                        'sim_id'=>$simId, 
                        'dialog_id'=>$dialogId,
                        'point_id' =>$pointId
                        ));
	}
	
	public static function getLogDataDoialog() {
	
		$sql = "            SELECT
				      `log_dialog`.`sim_id`
					 , `ceil`.`code` AS `p_code`
				     , `ceil`.`title` AS `p_title`
				     , `points`.`code`
				     , `points`.`title`
				     , `type_scale`.`value` AS `type_scale`
				     , `points`.`scale`
					 , `characters_points`.add_value
					 , `log_dialog`.`dialog_id`
				     , `dialogs`.`code` AS `dialog_code`
				     , `dialogs`.`step_number`
				     , `dialogs`.`replica_number`
				FROM
				  `log_dialog`
				JOIN `dialogs`
				ON `log_dialog`.dialog_id = `dialogs`.id
				JOIN `characters_points_titles` AS `points`
				ON `points`.id = `log_dialog`.point_id
				JOIN `characters_points`
				ON `log_dialog`.`dialog_id` = `characters_points`.`dialog_id`
				LEFT JOIN `characters_points_titles` AS `ceil`
				ON `points`.`parent_id` = `ceil`.id
				LEFT JOIN `type_scale`
				ON `type_scale`.id = `points`.`type_scale`
				  GROUP BY `log_dialog`.id
				";
		$connection=Yii::app()->db;
		$command=$connection->createCommand($sql);
		$rows=$command->queryAll();
		return $rows;
	}
	
	public static function getDialogCSV() {
	
		$data = self::getLogDataDoialog();
		foreach ($data as  $k=>$row) {
			$data[$k]['p_title'] = Strings::toWin($data[$k]['p_title']);
			$data[$k]['title'] = Strings::toWin($data[$k]['title']);
			$data[$k]['scale'] = Strings::toWin(str_replace('.', ',', $data[$k]['scale']));
		}
		$csv = new ECSVExport($data, true, true, ';');
		$csv->setHeaders(array(
				'sim_id'        => Strings::toWin('id_симуляции'),
				'p_code'          => Strings::toWin('Номер цели обучения'),
				'p_title'       => Strings::toWin('Наименование цели обучения'),
				'code' => Strings::toWin('Номер поведения'),
				'title'          => Strings::toWin('Наименование поведения'),
				'type_scale'         => Strings::toWin('Тип поведения'),
				'scale'      => Strings::toWin('Вес поведения'),
				'add_value'      => Strings::toWin('Проявление'),
				'dialog_id'           => Strings::toWin('Вызвавшая реплика (id_записи)'),
				'dialog_code'    => Strings::toWin('Вызвавшая реплика (Код события)'),
				'step_number'    => Strings::toWin('Вызвавшая реплика (номер шага)'),
				'replica_number'    => Strings::toWin('Вызвавшая реплика (номер реплики)')
		));
		$content = $csv->toCSV();
		$filename = 'data.csv';
                Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=windows-1251", false);
        
	}
        
        public static function getDataDialogAvg() {
            
            $sql = "
                    SELECT 
				`log_dialog`.`sim_id`
                                ,`points`.`code`
				,`type_scale`.`value` AS `type_scale`
				, round(avg(`characters_points`.`add_value`)*`points`.scale, 2) as avg
                                FROM
                                log_dialog
                                LEFT JOIN characters_points ON log_dialog.dialog_id = characters_points.dialog_id
                                LEFT JOIN characters_points_titles AS points ON  characters_points.point_id = points.id
                                LEFT JOIN type_scale ON points.type_scale = type_scale.id 
                                GROUP BY `log_dialog`.`sim_id`, `code` ORDER BY `log_dialog`.`sim_id`
				";
		$connection = Yii::app()->db;
		$command = $connection->createCommand( $sql );
		$rows = $command->queryAll();
                
		return $rows;
            
        }
        
        public static function getDialogAvgCSV() {
                $data = self::getDataDialogAvg();
                //var_dump($data);
                //exit("Exit");
		foreach ($data as  $k=>$row) {
			$data[$k]['avg'] = Strings::toWin(str_replace('.', ',', $data[$k]['avg']));
		}
		$csv = new ECSVExport($data, true, true, ';');
		$csv->setHeaders(array(
				'sim_id'        => Strings::toWin('id_симуляции'),
                                'code' => Strings::toWin('Номер поведения'),
                                'type_scale'         => Strings::toWin('Тип поведения'),
                    		'agv' => Strings::toWin('Номер поведения')				
		));
		$content = $csv->toCSV();
		$filename = 'data.csv';
                Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=windows-1251", false);
        }
	
}