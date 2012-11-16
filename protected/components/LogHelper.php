<?php

class LogHelper {

    const ACTION_CLOSE = 0;

    const ACTION_OPEN = 1;

    const ACTION_SWITCH = 2;

    protected static $codes_documents = array(40,41,42);

    protected static $codes_mail = array(10,11,12,13,14);

	public function __construct() {
		
	}

    /**
     * Пишет лог для Логирование расчета оценки - детально
     * @param int $dialogId ID - диалога
     * @param int $simId ID - Симуляции
     * @param int $pointId ID - Поинта с таблицы `characters_points_titles`
     */
    public static function setLogDoialog( $dialogId, $simId, $pointId ) {

		$comand = Yii::app()->db->createCommand();
		$comand->insert( "log_dialog" , array(
                         'sim_id'    => $simId,
                         'dialog_id' => $dialogId,
                         'point_id'  => $pointId
                        ));
	}
	
	public static function getLogDataDoialog() {
	
        $data = Yii::app()->db->createCommand()
            ->select('l.sim_id
					 , p2.code as p_code
				     , p2.title AS p_title
				     , p.code
				     , p.title
				     , t.value as type_scale
				     , p.scale
					 , c.add_value
					 , d.excel_id as dialog_id
				     , d.code AS dialog_code
				     , d.step_number
				     , d.replica_number')
            ->from('log_dialog l')
            ->join('characters_points c', 'l.point_id = c.point_id and l.dialog_id = c.dialog_id')
            ->join('dialogs d', 'd.id = c.dialog_id and d.id = l.dialog_id')
            ->join('characters_points_titles p', 'p.id = l.point_id and p.id = c.point_id')
            ->join('characters_points_titles p2', 'p2.id = p.parent_id')
            ->leftJoin('type_scale t', 'p.type_scale = t.id')
            ->order('l.id')
            ->queryAll();

        return $data;
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

    public static function setDocumentsLog( $simId, $logs ) {
        
        if (!is_array($logs)) return false;
        foreach( $logs as $log ) {

            if( in_array( $log[0], self::$codes_documents ) || in_array( $log[1], self::$codes_documents ) ) {

                if(!isset($log[4]['fileId'])) continue;
                
                if( self::ACTION_OPEN == $log[2] ){

                    $comand = Yii::app()->db->createCommand();
                    $comand->insert( "log_documents" , array(
                        'sim_id'    => $simId,
                        'file_id'   => $log[4]['fileId'],
                        'start_time'=> date("H:i:s", $log[3])
                    ));
                } elseif( self::ACTION_CLOSE == $log[2] ) {
                    //Yii::log(var_export($log, true), 'info');
                    $comand = Yii::app()->db->createCommand();

                    $comand->update( "log_documents" , array(
                        'end_time'  => date("H:i:s", $log[3])
                        ), "`file_id` = {$log[4]['fileId']} AND
                        `end_time` = '00:00:00' ORDER BY `id` DESC LIMIT 1");
                } else {
                    throw new Exception("Ошибка");//TODO:Описание доделать
                }
            }
        }

        return true;
    }
    
    public static function getDocumentsLog() {
        
        $data = Yii::app()
                ->db
                ->createCommand()
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
    
    public static function setMailLog( $simId, $logs ) {
        //Yii::log(var_export($logs, true), 'info');
        if (!is_array($logs)) return false;
        foreach( $logs as $log ) {
            
            if( in_array( $log[0], self::$codes_mail ) || in_array( $log[1], self::$codes_mail ) ) {

                //if(!isset($log[4]['mailId'])) continue;
                //Yii::log(var_export($log, true), 'info');
                if( self::ACTION_OPEN == $log[2] ){
                    //Yii::log(var_export($log, true), 'info');
                    $comand = Yii::app()->db->createCommand();
                    $comand->insert( "log_mail" , array(
                        'sim_id'    => $simId,
                        'mail_id'   => $log[4]['mailId'],
                        'window'   => $log[1],
                        'start_time'  => date("H:i:s", $log[3])
                    ));
                } elseif( self::ACTION_CLOSE == $log[2]) {

                    $comand = Yii::app()->db->createCommand();

                    $comand->update( "log_mail" , array(
                        'end_time'  => date("H:i:s", $log[3])
                        ), "`mail_id` = {$log[4]['mailId']} AND
                        `end_time` = '00:00:00' ORDER BY `id` DESC LIMIT 1");
                    //Yii::log(var_export($res, true), 'info');
                } elseif( $log[2] == self::ACTION_SWITCH ) {
                    
                    $comand = Yii::app()->db->createCommand();

                    $res = $comand->update( "log_mail" , array(
                        'end_time'  => date( "H:i:s", $log[3] )
                    ), "`end_time` = '00:00:00' ORDER BY `id` DESC LIMIT 1");
                    //Yii::log(var_export($res, true), 'info');
                    //if($res =! 1){
                        $comand->insert( "log_mail" , array(
                            'sim_id'    => $simId,
                            'mail_id'   => $log[4]['mailId'],
                            'window'   => $log[1],
                            'start_time'  => date("H:i:s", $log[3])
                        ));
                    //}
                } else {
                    //Yii::log("NO ACTION_OPEN OR ACTION_CLOSE", 'info');
                    throw new Exception("Ошибка");//TODO:Описание доделать
                }
            }
        }
        return true;
    }
	
    public static function getMailInBoxLog() {

        $data = Yii::app()
            ->db
            ->createCommand()
            ->select('l.sim_id,
                        m.code,
                      l.window,
                  l.start_time,
                    l.end_time')
            ->from('log_mail l')
            ->join('mail_box m', 'l.mail_id = m.id')
            ->where('l.window != 13')
            ->queryAll();

        return $data;
    }
    
    public static function getMailInBoxCSV() {

        $data = self::getMailInBoxLog();

        foreach ($data as  $k=>$row) {
            $data[$k]['window'] = WindowLogger::$subScreens[$data[$k]['window']];
        }
        $csv = new ECSVExport($data, true, true, ';');
        $csv->setHeaders(array(
            'sim_id'     => Strings::toWin('id_симуляции'),
            'code'       => Strings::toWin('Код входящего письма'),
            'window'     => Strings::toWin('Тип просмотра'),
            'start_time' => Strings::toWin('Игровое время - start'),
            'end_time'   => Strings::toWin('Игровое время - end')
        ));
        $content = $csv->toCSV();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=windows-1251", false);

    }
    
    public static function getMailInBoxAVG() {

        $data = Yii::app()
            ->db
            ->createCommand()
            ->select("m.sim_id
                    , m.code
                    , g.name
                    , if(m.readed = 0, 'Нет', 'Да') AS readed
                    , if(m.plan = 0, 'Нет', 'Да') AS plan
                    , if(m.reply = 0, 'Нет', 'Да') AS reply")
            ->from('mail_box m')
            ->join('mail_group g', 'm.group_id = g.id')
            ->where('type = 1 or type = 3')
            ->order('m.id')
            ->queryAll();

        return $data;

    }
    
    public static function getMailInBoxAvgCSV() {

        $data = self::getMailInBoxAVG();

        foreach ($data as  $k=>$row) {
            $data[$k]['name'] = Strings::toWin($data[$k]['name']);
            $data[$k]['readed'] = Strings::toWin($data[$k]['readed']);
            $data[$k]['plan'] = Strings::toWin($data[$k]['plan']);
            $data[$k]['reply'] = Strings::toWin($data[$k]['reply']);
        }
        $csv = new ECSVExport($data, true, true, ';');
        $csv->setHeaders(array(
            'sim_id'     => Strings::toWin('id_симуляции'),
            'code'       => Strings::toWin('Код входящего письма'),
            'name'     => Strings::toWin('Папка мейл-клиента'),
            'readed' => Strings::toWin('Письмо прочтено (да/нет)'),
            'plan'   => Strings::toWin('Письмо запланировано (да/нет)'),
            'reply'   => Strings::toWin('На письмо отправлен ответ')
        ));
        $content = $csv->toCSV();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=windows-1251", false);

    }
    
    public static function getMailOutBoxLog() {
        
    }
    
    public static function getMailOutBoxCSV() {
        
    }
    
    public static function getMailOutBoxAVG() {
        
    }
    
    public static function getMailOutBoxAvgCSV() {
        
    }
}