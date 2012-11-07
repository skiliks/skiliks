<?php

class LogHelper {

    const ACTION_CLOSE = 0;

    const ACTION_OPEN = 1;

    protected static $codes = array(4,6,7);

	public function __construct() {
		
	}

    /**
     * Пишет лог для Логирование расчета оценки - детально
     * @param int $dialogId ID - диалога
     * @param int $simId ID - Симуляции
     * @param int $pointId ID - Поинта с таблицы `characters_points_titles`
     */
    public static function setLogDoialog($dialogId, $simId, $pointId) {

		$comand = Yii::app()->db->createCommand();
		$comand->insert( "log_dialog" , array(
                         'sim_id'    => $simId,
                         'dialog_id' => $dialogId,
                         'point_id'  => $pointId
                        ));
	}
	
	public static function getLogDataDoialog() {
	
		$sql = "  SELECT
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
            
           /*     $sql = "
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
            * 
            */
            
            $data = Yii::app()->db->createCommand()
            ->select('l.sim_id,
                      p.code,
                      t.value as type_scale, 
                      round(avg(c.add_value)*p.scale, 2) as avg')
            ->from('log_dialog l')
            ->leftJoin('characters_points c', 'l.dialog_id = c.dialog_id')
            ->leftJoin('characters_points_titles p', 'c.point_id = p.id')
            ->leftJoin('type_scale t', 'p.type_scale = t.id')
            ->group("l.sim_id, p.code")
            ->order("l.sim_id")        
	    ->queryAll();
            
            return $data;
        }
        
        public static function getDialogAvgCSV() {

            $data = self::getDataDialogAvg();

            foreach ($data as  $k=>$row) {
                $data[$k]['avg'] = Strings::toWin(str_replace('.', ',', $data[$k]['avg']));
            }
            $csv = new ECSVExport($data, true, true, ';');
            $csv->setHeaders(array(
                    'sim_id'     => Strings::toWin('id_симуляции'),
                    'code'       => Strings::toWin('Номер поведения'),
                    'type_scale' => Strings::toWin('Тип поведения'),
                    'avg'        => Strings::toWin('Оценка по поведению')
            ));
            $content = $csv->toCSV();
            $filename = 'data.csv';
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=windows-1251", false);
        }

    public static function setDocumentsLog($simId, $logs){

        foreach( $logs as $log ) {
            //var_dump($log);
            //exit;
            if( in_array($log[0], self::$codes) || in_array($log[1], self::$codes)) {
                //var_dump($log);
                //var_dump($log[4]['fileId']);
                //var_dump($log[4]);
                //exit;
                if(!isset($log[4]['fileId'])) continue;
                //var_dump(self::ACTION_OPEN);
                //var_dump($log[2]);
                if( self::ACTION_OPEN == $log[2] ){
                    //var_dump($log[4]['fileId']);
                    $comand = Yii::app()->db->createCommand();
                    $comand->insert( "log_documents" , array(
                        'sim_id'    => $simId,
                        'file_id' => $log[4]['fileId'],
                        'start_time'  => date("H:i:s", $log[3])
                    ));
                } elseif( self::ACTION_CLOSE == $log[2] ) {
                    /*$start_time_id  = Yii::app()->db->createCommand()
                        ->select( 'id ' )
                        ->from( 'log_documents' )
                        ->where( ' end_time is null ' )
                        ->queryRow();
                    */
                    $comand = Yii::app()->db->createCommand();

                    $comand->update( "log_documents" , array(
                        'end_time'  => date("H:i:s", $log[3])
                        ), "`file_id` = {$log[4]['fileId']} AND
                        `end_time` = '00:00:00' ORDER BY `id` DESC LIMIT 1");
                    var_dump($comand->pdoStatement);
                    var_dump($comand->params);
                } else {
                    throw new Exception("Ошибка");//TODO:Описание доделать
                }
            }
        }


    }
    
    public static function getDocumentsLog() {
        
        /*$sql = "
                 SELECT `l`.`sim_id`
			, `t`.`code`
			, `t`.`fileName`
			, `l`.start_time
			, `l`.end_time
                        FROM `log_documents` as l
                        JOIN `my_documents` as d 
                        ON `l`.`file_id` = `d`.`id`
                        JOIN `my_documents_template` as t
                        ON `d`.`template_id` = `t`.`id`
                ";
        $connection = Yii::app()->db;
        $command = $connection->createCommand( $sql );
        $rows = $command->queryAll();

        return $rows;
         * 
         */
        $data = Yii::app()->db->createCommand()
            ->select('l.sim_id,
                      t.code,
                      t.fileName, 
                      l.start_time,
                      l.end_time')
            ->from('log_documents l')
            ->join('my_documents d', 'l.file_id = d.id')
            ->join('my_documents_template t', 'd.template_id = t.id')    
	    ->queryAll();
        return $data;
    }
    
    public static function getDocumentsCSV() {
        
        $data = self::getDocumentsLog();

        foreach ($data as  $k=>$row) {
            $data[$k]['fileName'] = Strings::toWin(str_replace('.', ',', $data[$k]['fileName']));
        }
        $csv = new ECSVExport($data, true, true, ';');
        $csv->setHeaders(array(
                'sim_id'     => Strings::toWin('id_симуляции'),
                'code'       => Strings::toWin('Код документа'),
                'fileName'   => Strings::toWin('Наименование документа'),
                'start_time' => Strings::toWin('Игровое время - start'),
                'end_time'   => Strings::toWin('Игровое время - end')
        ));
        $content = $csv->toCSV();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=windows-1251", false);
        
    }
	
}