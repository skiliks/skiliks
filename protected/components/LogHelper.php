<?php

class LogHelper {

    const ACTION_CLOSE = "0"; //Закрытие окна

    const ACTION_OPEN = "1"; //Открытие окна

    const ACTION_SWITCH = "2"; //Переход в рамках окна
    
    const ACTION_ACTIVATED = "activated"; //Активация окна
    
    const ACTION_DEACTIVATED = "deactivated"; //Деактивация окна

    const RETURN_DATA = 'json'; //Тип возвращаемого значения JSON

    const RETURN_CSV = 'csv'; //Тип возвращаемого значения CSV
    
    const LOGIN = true; //Писать лог в файл? true - да, false - нет
    
    protected static $codes_documents = array(40,41,42);

    protected static $codes_mail = array(10,11,12,13,14);
    
    protected static $screens = array(
        1 => 'main screen', 
        3 => 'plan', 
        10 => 'mail',
        20 => 'phone',
        30 => 'visitor', 
        40 => 'documents'
    );
    
    protected static $subScreens = array(
        1 => 'main screen',
        3 => 'plan',
        11 => 'mail main',
        12 => 'mail preview',
        13 => 'mail new',
        14 => 'mail plan',
        21 => 'phone main',
        23 => 'phone talk',
        24 => 'phone call',
        31 => 'visitor entrance',
        32 => 'visitor talk',
        41 => 'documents main',
        42 => 'documents files'
    );

	private function __construct() {
		
	}
    
    public static function setLog($simId, $logs) {
        
        if(self::LOGIN) {
            if(is_array($logs)) {
                $sparator = ';';
                $end = "\r\n";
                if(is_dir(__DIR__.'/../runtime/')) {
                    $file = fopen(__DIR__.'/../runtime/windows.log', "a+");
                    foreach ($logs as $log) {
                        $csv = '';
                        $csv .= date("d.m.Y H:i:s", time()).$sparator; //Дата и время на сервере 
                        $csv .= $simId.$sparator; //id симуляции
                        $csv .= $log[0].$sparator; //Активное окно
                        $csv .= $log[1].$sparator; //Активное под окно
                        $csv .= $log[2].$sparator; //Действие
                        $csv .= $log[3].$sparator; //Игровое время
                        $csv .= (empty($log[4]['mailId'])?'':$log[4]['mailId']).$sparator;// Дополнительный параметр mailId
                        $csv .= (empty($log[4]['fileId'])?'':$log[4]['fileId']).$end;// Дополнительный параметр fileId
                        fwrite($file, $csv);
                    } 
                    fclose($file);
                } else {
                    throw new Exception("Не правильный путь ".__DIR__.'/../runtime/');
                }
            }
        }
  
    }

    public static function logFilter($logs) {
        
        if(!is_array($logs)) return false;
        
        foreach ($logs as $key => $value) {
            if(isset($logs[$key-1])){
                if($logs[$key][0] == $logs[$key-1][0] AND $logs[$key][1] == $logs[$key-1][1] AND $logs[$key][2] != $logs[$key-1][2] AND $logs[$key][3] == $logs[$key-1][3]){
                    unset($logs[$key]);
                    unset($logs[$key-1]);
                } else {
                    continue;
                }
            }else{
                continue;
            }
        }
        
        return $logs;
    }

    private static function order($order_col, $columns, $order_type = "asc") {
        if(is_array($columns)){
            
        } else {
            throw new Exception('Параметр $columns не задан!');
        }
        if (in_array($order_type, array('asc', 'desc'))) {
            return "{$order_col} {$order_type}";
        }else{
            throw new Exception("Тип сортировки '$order_type' неизвестен!");
        }
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

	public static function getDialogDetail($return, $params=array()) {
        $col = array(
            'sim_id'=>'l.sim_id',
            'p_code'=>'p2.code',
            'p_title'=>'p2.title',
            'code'=>'p.code',
            'title'=>'p.title',
            'type_scale'=>'t.value',
            'scale'=>'p.scale',
            'add_value'=>'c.add_value',
            'dialog_id'=>'d.excel_id',
            'dialog_code'=>'d.code',
            'step_number'=>'d.step_number',
            'replica_number'=>'d.replica_number'
        );
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
            ->order($order)
            ->queryAll();

        foreach ($data as  $k=>$row) {
            $data[$k]['p_title'] = Strings::toWin($data[$k]['p_title']);
            $data[$k]['title'] = Strings::toWin($data[$k]['title']);
            $data[$k]['scale'] = Strings::toWin(str_replace('.', ',', $data[$k]['scale']));
        }

        if(self::RETURN_DATA == $return){
            return $data;
        } elseif (self::RETURN_CSV == $return){
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
        }else{
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;
	}

     public static function getDialogAggregate($return) {

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

            foreach ($data as  $k=>$row) {
                $data[$k]['avg'] = Strings::toWin(str_replace('.', ',', $data[$k]['avg']));
            }

            if(self::RETURN_DATA == $return){
                 return $data;
            } elseif (self::RETURN_CSV == $return) {

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
            } else {
                throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
            }
         return true;
        }

    public static function setDocumentsLog( $simId, $logs ) {
        
        if (!is_array($logs)) return false;
        //Yii::log(var_export($logs, true), 'info');
        foreach( $logs as $log ) {
                
            if( in_array( $log[0], self::$codes_documents ) || in_array( $log[1], self::$codes_documents ) ) {

                if(!isset($log[4]['fileId'])) continue;
                
                if( self::ACTION_OPEN == (string)$log[2] OR self::ACTION_ACTIVATED == (string)$log[2]){

                    $comand = Yii::app()->db->createCommand();
                    $comand->insert( "log_documents" , array(
                        'sim_id'    => $simId,
                        'file_id'   => $log[4]['fileId'],
                        'start_time'=> date("H:i:s", $log[3])
                    ));
                } elseif( self::ACTION_CLOSE == (string)$log[2] OR self::ACTION_DEACTIVATED == (string)$log[2]) {
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

    public static function getDocuments($return) {

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
            ->order('l.id')
            ->queryAll();

        foreach ($data as  $k=>$row) {
            $data[$k]['fileName'] = Strings::toWin(str_replace('.', ',', $data[$k]['fileName']));
        }

        if(self::RETURN_DATA == $return){
            return $data;
        } elseif (self::RETURN_CSV == $return) {

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
    } else {
        throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
    }
    return true;
        
    }
    
    public static function setMailLog( $simId, $logs ) {
            
        //Yii::log(var_export($logs, true), 'info');
        if (!is_array($logs)) return false;
        foreach( $logs as $log ) {
            
            if( in_array( $log[0], self::$codes_mail ) || in_array( $log[1], self::$codes_mail ) ) {
                $comand = Yii::app()->db->createCommand();
                //if(!isset($log[4]['mailId'])) continue;
                
                if( self::ACTION_OPEN == (string)$log[2] OR self::ACTION_ACTIVATED == (string)$log[2] ) {
                    Yii::log(var_export($log, true), 'info');
                    Yii::log(var_export($log[2], true), 'info');
                    $comand->insert( "log_mail" , array(
                        'sim_id'    => $simId,
                        'mail_id'   => empty($log[4]['mailId'])?NULL:$log[4]['mailId'],
                        'window'   => $log[1],
                        'start_time'  => date("H:i:s", $log[3])
                    ));
                    continue;
                    
                } elseif( self::ACTION_CLOSE == (string)$log[2] OR self::ACTION_DEACTIVATED == (string)$log[2] ) {
                    
                    if($log[1] != 13) {
                        //Yii::log(var_export($log, true), 'info');
                        $comand->update( "log_mail" , array(
                        'end_time'  => date("H:i:s", $log[3])
                        ), "`mail_id` = {$log[4]['mailId']} AND `end_time` = '00:00:00' AND `sim_id` = {$simId} ORDER BY `id` DESC LIMIT 1");
                        continue;
                        
                    } else {
                        //Yii::log(var_export($log, true), 'info');
                        $comand->update( "log_mail" , array(
                        'end_time'  => date("H:i:s", $log[3]),
                        'mail_id'  => empty($log[4]['mailId'])?NULL:$log[4]['mailId']    
                        ), "`mail_id` is null AND `end_time` = '00:00:00' AND `sim_id` = {$simId} ORDER BY `id` DESC LIMIT 1");
                        continue;
                         
                    }
                    
                } elseif( self::ACTION_SWITCH == (string)$log[2] ) {
                    //Yii::log($log, 'info');
                    $comand->update( "log_mail" , array(
                        'end_time'  => date( "H:i:s", $log[3] )
                    ), "`end_time` = '00:00:00' AND `sim_id` = {$simId} ORDER BY `id` DESC LIMIT 1");
                    
                        $comand->insert( "log_mail" , array(
                            'sim_id'    => $simId,
                            'mail_id'   => $log[4]['mailId'],
                            'window'   => $log[1],
                            'start_time'  => date("H:i:s", $log[3])
                        ));
                    
                } else {
                    //Yii::log("NO ACTION_OPEN OR ACTION_CLOSE", 'info');
                    throw new Exception("Ошибка");//TODO:Описание доделать
                }
            }
        }
        
        return true;
    }

    public static function getMailInDetail($return) {

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
            ->order('l.id')
            ->queryAll();

        foreach ($data as  $k=>$row) {
            $data[$k]['window'] = self::$subScreens[$data[$k]['window']];
        }

        if(self::RETURN_DATA == $return){
            return $data;
        } elseif (self::RETURN_CSV == $return) {

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
        } else {
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;
    }

    public static function getMailInAggregate($return) {

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

        foreach ($data as  $k=>$row) {
            $data[$k]['name'] = Strings::toWin($data[$k]['name']);
            $data[$k]['readed'] = Strings::toWin($data[$k]['readed']);
            $data[$k]['plan'] = Strings::toWin($data[$k]['plan']);
            $data[$k]['reply'] = Strings::toWin($data[$k]['reply']);
        }

        if(self::RETURN_DATA == $return){
            return $data;
        } elseif (self::RETURN_CSV == $return) {

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
        } else {
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;

    }

    public static function getMailOutDetail($return) {

        $data = Yii::app()
            ->db
            ->createCommand()
            ->select("l.sim_id
                     , ifnull(l.mail_id, '-') AS mail_id
                     , if(m.group_id = 3, 'Да', 'Нет') AS send
                     , l.start_time
                     , l.end_time")
            ->from('log_mail l')
            ->leftJoin('mail_box m', 'l.mail_id = m.id')
            ->where('l.window = 13')
            ->order('l.id')
            ->queryAll();

        foreach ($data as  $k=>$row) {
            $data[$k]['send'] = Strings::toWin($data[$k]['send']);
        }

        if(self::RETURN_DATA == $return){
            return $data;
        } elseif (self::RETURN_CSV == $return) {

        $csv = new ECSVExport($data, true, true, ';');
        $csv->setHeaders(array(
            'sim_id'     => Strings::toWin('id_симуляции'),
            'mail_id'       => Strings::toWin('id_исходящего письма'),
            'send'     => Strings::toWin('Отправлено'),
            'start_time' => Strings::toWin('Игровое время - start'),
            'end_time'   => Strings::toWin('Игровое время - end')
        ));
        $content = $csv->toCSV();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=windows-1251", false);
        } else {
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;
    }

    public static function getMailOutAggregate($return) {

        $data = Yii::app()
            ->db
            ->createCommand()
            ->select("l.sim_id
                    , ifnull(l.mail_id, '-') AS mail_id
                    , if(m.group_id = 3, 'Да', 'Нет') AS send
                    , ifnull(group_concat(DISTINCT r.receiver_id), '-') AS receivers
                    , ifnull(group_concat(DISTINCT c.receiver_id), '-') AS copies
                    , ifnull(s.name, '-') AS subject
                    , ifnull(t.code, '-') AS code")
            ->from('log_mail l')
            ->leftJoin('mail_box m', 'l.mail_id = m.id')
            ->leftJoin('mail_receivers r', 'l.mail_id = r.mail_id')
            ->leftJoin('mail_copies c', 'l.mail_id = c.mail_id')
            ->leftJoin('mail_attachments a', 'm.id = a.mail_id')
            ->leftJoin('my_documents d', 'a.file_id = d.id')
            ->leftJoin('my_documents_template t', 'd.template_id = t.id')
            ->leftJoin('mail_themes s', 'm.subject_id = s.id')    
            ->where('l.window = 13 AND l.mail_id IS NOT NULL')
            ->group('l.mail_id')
            ->order('l.id')
            ->queryAll();

        foreach ($data as  $k=>$row) {
            $data[$k]['send'] = Strings::toWin($data[$k]['send']);
            $data[$k]['subject'] = Strings::toWin($data[$k]['subject']);
        }

        if(self::RETURN_DATA == $return){
            return $data;
        } elseif (self::RETURN_CSV == $return) {

        $csv = new ECSVExport($data, true, true, ';');
        $csv->setHeaders(array(
            'sim_id'     => Strings::toWin('id_симуляции'),
            'mail_id'       => Strings::toWin('id_исходящего письма'),
            'send'     => Strings::toWin('Отправлено'),
            'receivers' => Strings::toWin('Кому'),
            'copies'   => Strings::toWin('Копия'),
            'subject'   => Strings::toWin('Тема'),
            'code'   => Strings::toWin('Код вложения')
        ));
        $content = $csv->toCSV();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=windows-1251", false);
        } else {
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;
    }
    
    public static function setWindowsLog( $simId, $logs ) {
        if (!is_array($logs)) return false;
        foreach( $logs as $log ) {
            
                $comand = Yii::app()->db->createCommand();
                //if(!isset($log[4]['mailId'])) continue;
                //Yii::log(var_export($log, true), 'info');
                if( self::ACTION_OPEN == (string)$log[2] || self::ACTION_ACTIVATED == (string)$log[2]) {
//                    $comand->update( "log_windows" , array(
//                        'end_time'  => date("H:i:s", $log[3])
//                        ), "`end_time` = '00:00:00' AND `sim_id` = {$simId} ORDER BY `id` DESC LIMIT 1");
                    $comand->insert( "log_windows" , array(
                        'sim_id'    => $simId,
                        'window'   => $log[0],
                        'sub_window'   => $log[1],
                        'start_time'  => date("H:i:s", $log[3])
                    ));
                    continue;
                    
                } elseif( self::ACTION_CLOSE == (string)$log[2] || self::ACTION_DEACTIVATED == (string)$log[2] ) {

                        $comand->update( "log_windows" , array(
                        'end_time'  => date("H:i:s", $log[3])
                        ), "`end_time` = '00:00:00' AND `sim_id` = {$simId} ORDER BY `id` DESC LIMIT 1");
                        continue;
                } elseif (self::ACTION_SWITCH == (string)$log[2]) { 
                
                    continue;
                    
                } else {
                    
                    throw new CException("Ошибка");//TODO:Описание доделать
                }
            }
            
        return true;
    }

    public static function getWindows($return) {

        $data = Yii::app()
            ->db
            ->createCommand()
            ->select("s.user_id
                    , u.email
                    , s.start
                    , s.end
                    , s.id
                    , l.window
                    , l.sub_window
                    , l.start_time
                    , l.end_time")
            ->from('log_windows l')
            ->leftJoin('simulations s', 's.id = l.sim_id')
            ->leftJoin('users u', 'u.id = s.user_id')
            ->order('l.id')
            ->queryAll();

        foreach ($data as  $k=>$row) {
            $data[$k]['start'] = date("d.m.Y H:i:s", $data[$k]['start']);
            $data[$k]['end'] = date("d.m.Y H:i:s", $data[$k]['end']);
            $data[$k]['window'] = self::$screens[$data[$k]['window']];
            $data[$k]['sub_window'] = self::$subScreens[$data[$k]['sub_window']];
        }
        if(self::RETURN_DATA == $return){
            return $data;
        } elseif (self::RETURN_CSV == $return) {
        $csv = new ECSVExport($data, true, true, ';');
        $csv->setHeaders(array(
            'user_id'           => Strings::toWin('id_пользователя'), 
            'email'             => Strings::toWin('email'),
            'start'             => Strings::toWin('дата старта симуляции'),
            'end'               => Strings::toWin('дата окончания симуляции'),                
            'id'                => Strings::toWin('id_симуляции'),
            'window'      => Strings::toWin('Активное окно'),
            'sub_window'   => Strings::toWin('Активное подокно'),
            'start_time'         => Strings::toWin('Игровое время - start'),
            'end_time'           => Strings::toWin('Игровое время - end')                
        ));
        $content = $csv->toCSV();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=windows-1251", false);
        } else {
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;
    }
}