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
    
    public $bom = "0xEF 0xBB 0xBF";
        
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
    
    protected static $actions = array(
        0             => 'close',
        1             => 'open',
        2             => 'switch',
        'activated'   => 'activated',
        'deactivated' => 'deactivated',
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
                        $hours = floor($log[3]/3600);
                        $minutes = floor($log[3]/60) - $hours*60;
                        $seconds = $log[3] - 3600*$hours - 60*$minutes;
                        
                        $csv = array();
                        $csv[] = date("d.m.Y H:i:s", time()); //Дата и время на сервере 
                        $csv[] = $simId; //id симуляции
                        $csv[] = $log[0]; //Активное окно
                        $csv[] = $log[1]; //Активное под окно
                        $csv[] = $log[2]; //Действие
                        $csv[] = $log[3]; //Игровое время
                        $csv[] = (empty($log[4]['mailId'])?'':$log[4]['mailId']);// Дополнительный параметр mailId
                        $csv[] = (empty($log[4]['fileId'])?'':$log[4]['fileId']);// Дополнительный параметр fileId
                        $csv[] = 'numbers => values';
                        $csv[] = sprintf(
                             '%02s:%02s:%02s %s',
                             $hours,
                             $minutes,
                             $seconds,
                             $sparator
                        );
                        $csv[] = self::$screens[$log[0]];
                        $csv[] = self::$subScreens[$log[1]];
                        $csv[] = self::$actions[$log[2]];
                        // todo: use explode()
                        fwrite($file, implode($sparator, $csv).$end);
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

        $new_logs = array();
        foreach ($logs as $key => $value) {
            if(isset($logs[$key-1])){
                if($logs[$key][0] == $logs[$key-1][0] AND $logs[$key][1] == $logs[$key-1][1] AND $logs[$key][2] != $logs[$key-1][2] AND $logs[$key][3] == $logs[$key-1][3]){
                    continue;
                } else {
                    array_push($new_logs, $value);
                }
            }else{
                array_push($new_logs, $value);
            }
        }
        Yii::log('Window: ' . CJSON::encode($new_logs));
        return $new_logs;
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
        $data['data'] = Yii::app()->db->createCommand()
            ->select(' l.sim_id
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
                       , d.replica_number
		')
            ->from('log_dialog l')
            ->join('characters_points c', 'l.point_id = c.point_id and l.dialog_id = c.dialog_id')
            ->join('dialogs d', 'd.id = c.dialog_id and d.id = l.dialog_id')
            ->join('characters_points_titles p', 'p.id = l.point_id and p.id = c.point_id')
            ->join('characters_points_titles p2', 'p2.id = p.parent_id')
            ->leftJoin('type_scale t', 'p.type_scale = t.id')
            ->order('l.id')
            ->queryAll();
        


        foreach ($data['data'] as  $k=>$row) {
            //$data['data'][$k]['p_title'] = Strings::toWin($data['data'][$k]['p_title']);
            //$data['data'][$k]['title'] = Strings::toWin($data['data'][$k]['title']);
            $data['data'][$k]['scale'] = Strings::toWin(str_replace('.', ',', $data['data'][$k]['scale']));
        }
        $headers = array(
                'sim_id'        => 'id_симуляции',
                'p_code'          => 'Номер цели обучения',
                'p_title'       => 'Наименование цели обучения',
                'code' => 'Номер поведения',
                'title'          => 'Наименование поведения',
                'type_scale'         => 'Тип поведения',
                'scale'      => 'Вес поведения',
                'add_value'      => 'Проявление',
                'dialog_id'           => 'Вызвавшая реплика (id_записи)',
                'dialog_code'    => 'Вызвавшая реплика (Код события)',
                'step_number'    => 'Вызвавшая реплика (номер шага)',
                'replica_number'    => 'Вызвавшая реплика (номер реплики)'
            );
        if(self::RETURN_DATA == $return){
            $data['headers'] = $headers;
            $data['title'] = "Логирование расчета оценки - детально";
            return $data;
        } elseif (self::RETURN_CSV == $return){
            $csv = new ECSVExport($data['data'], true, true, ';');
            $csv->setHeaders($headers);
            $content = $csv->toCSVutf8BOM();
            $filename = 'data.csv';
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        }else{
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;
	}

     public static function getDialogAggregate($return) {

            $data['data'] = Yii::app()->db->createCommand()
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

            foreach ($data['data'] as  $k=>$row) {
                $data['data'][$k]['avg'] = str_replace('.', ',', $data['data'][$k]['avg']);
            }
            $headers = array(
                    'sim_id'     => 'id_симуляции',
                    'code'       => 'Номер поведения',
                    'type_scale' => 'Тип поведения',
                    'avg'        => 'Оценка по поведению'
            );
            if(self::RETURN_DATA == $return){
                $data['headers'] = $headers;
                $data['title'] = "Логирование расчета оценки - агрегированно";
                 return $data;
            } elseif (self::RETURN_CSV == $return) {
                
            $csv = new ECSVExport($data['data'], true, true, ';');
            $csv->setHeaders($headers);
            $content = $csv->toCSVutf8BOM();
            $filename = 'data.csv';
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
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

        $data['data'] = Yii::app()
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

        foreach ($data['data'] as  $k=>$row) {
            $data['data'][$k]['fileName'] = str_replace('.', ',', $data['data'][$k]['fileName']);
        }
        $headers = array(
                'sim_id'     => 'id_симуляции',
                'code'       => 'Код документа',
                'fileName'   => 'Наименование документа',
                'start_time' => 'Игровое время - start',
                'end_time'   => 'Игровое время - end'
        );
        if(self::RETURN_DATA == $return){
            $data['headers'] = $headers;
            $data['title'] = "";
            return $data;
        } elseif (self::RETURN_CSV == $return) {

        $csv = new ECSVExport($data['data'], true, true, ';');
        $csv->setHeaders($headers);
        $content = $csv->toCSVutf8BOM();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
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
                $command = Yii::app()->db->createCommand();
                //if(!isset($log[4]['mailId'])) continue;
                
                if( self::ACTION_OPEN == (string)$log[2] OR self::ACTION_ACTIVATED == (string)$log[2] ) {
                    Yii::log(var_export($log, true), 'info');
                    Yii::log(var_export($log[2], true), 'info');
                    $command->insert( "log_mail" , array(
                        'sim_id'    => $simId,
                        'mail_id'   => empty($log[4]['mailId'])?NULL:$log[4]['mailId'],
                        'window'   => $log[1],
                        'start_time'  => date("H:i:s", $log[3])
                    ));
                    continue;
                    
                } elseif( self::ACTION_CLOSE == (string)$log[2] OR self::ACTION_DEACTIVATED == (string)$log[2] ) {
                    
                    if($log[1] != 13) {
                        //Yii::log(var_export($log, true), 'info');
                        $command->update( "log_mail" , array(
                        'end_time'  => date("H:i:s", $log[3])
                        ), "`mail_id` = {$log[4]['mailId']} AND `end_time` = '00:00:00' AND `sim_id` = {$simId} ORDER BY `id` DESC LIMIT 1");
                        continue;
                        
                    } else {
                        //Yii::log(var_export($log, true), 'info');
                        $command->update( "log_mail" , array(
                        'end_time'  => date("H:i:s", $log[3]),
                        'mail_id'  => empty($log[4]['mailId'])?NULL:$log[4]['mailId']    
                        ), "`mail_id` is null AND `end_time` = '00:00:00' AND `sim_id` = {$simId} ORDER BY `id` DESC LIMIT 1");
                        continue;
                         
                    }
                    
                } elseif( self::ACTION_SWITCH == (string)$log[2] ) {
                    //Yii::log($log, 'info');
                    $command->update( "log_mail" , array(
                        'end_time'  => date( "H:i:s", $log[3] )
                    ), "`end_time` = '00:00:00' AND `sim_id` = {$simId} ORDER BY `id` DESC LIMIT 1");
                    
                        $command->insert( "log_mail" , array(
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

        $data['data'] = Yii::app()
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

        foreach ($data['data'] as  $k=>$row) {
            $data['data'][$k]['window'] = self::$subScreens[$data['data'][$k]['window']];
        }
        $headers = array(
            'sim_id'     => 'id_симуляции',
            'code'       => 'Код входящего письма',
            'window'     => 'Тип просмотра',
            'start_time' => 'Игровое время - start',
            'end_time'   => 'Игровое время - end'
        ); 
        if(self::RETURN_DATA == $return){
            $data['headers'] = $headers;
            $data['title'] = "Логирование работы с Входящими сообщениями - детально";
            return $data;
        } elseif (self::RETURN_CSV == $return) {
           
        $csv = new ECSVExport($data['data'], true, true, ';');
        $csv->setHeaders($headers);
        $content = $csv->toCSVutf8BOM();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;
    }

    public static function getMailInAggregate($return) {

        $data['data'] = Yii::app()
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

        $headers = array(
            'sim_id'     => 'id_симуляции',
            'code'       => 'Код входящего письма',
            'name'     => 'Папка мейл-клиента',
            'readed' => 'Письмо прочтено (да/нет)',
            'plan'   => 'Письмо запланировано (да/нет)',
            'reply'   => 'На письмо отправлен ответ'
        );
        if(self::RETURN_DATA == $return){
            $data['headers'] = $headers;
            $data['title'] = "Логирование работы с Входящими сообщениями - агрегированно";
            return $data;
        } elseif (self::RETURN_CSV == $return) {

        $csv = new ECSVExport($data['data'], true, true, ';');
        $csv->setHeaders($headers);
        $content = $csv->toCSVutf8BOM();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;

    }

    public static function getMailOutDetail($return) {

        $data['data'] = Yii::app()
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

        $headers = array(
            'sim_id'     => 'id_симуляции',
            'mail_id'       => 'id_исходящего письма',
            'send'     => 'Отправлено',
            'start_time' => 'Игровое время - start',
            'end_time'   => 'Игровое время - end'
        );
        if(self::RETURN_DATA == $return){
            $data['headers'] = $headers;
            $data['title'] = "Логирование работы с Исходящими сообщениями - детально";
            return $data;
        } elseif (self::RETURN_CSV == $return) {

        $csv = new ECSVExport($data['data'], true, true, ';');
        $csv->setHeaders($headers);
        $content = $csv->toCSVutf8BOM();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;
    }

    public static function getMailOutAggregate($return) {

        $data['data'] = Yii::app()
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
        $headers = array(
            'sim_id'     => 'id_симуляции',
            'mail_id'       => 'id_исходящего письма',
            'send'     => 'Отправлено',
            'receivers' => 'Кому',
            'copies'   => 'Копия',
            'subject'   => 'Тема',
            'code'   => 'Код вложения'
        );
        if(self::RETURN_DATA == $return){
            $data['headers'] = $headers;
            $data['title'] = "Логирование работы с Исходящими сообщениями - агрегированно";
            return $data;
        } elseif (self::RETURN_CSV == $return) {
        
        $csv = new ECSVExport($data['data'], true, true, ';');
        $csv->setHeaders($headers);
        $content = $csv->toCSVutf8BOM();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
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
                Yii::log(sprintf(
                        'Window log for sim %d: window "%s/%s", action %s, time %s, params %s',
                        $simId, $log[0], $log[1], $log[2],date("H:i:s", $log[3]), isset($log[4]) ? CJSON::encode($log[4]) : "none"
                    ),
                    'info', 'log');
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
                    $windows = LogWindows::model()->findAllByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simId));
                    if (!$windows) {
                        Yii::log('Trying to close unopened window', CLogger::LEVEL_WARNING);
                        continue;
                    }
                    foreach ($windows as $window) {
                        $window->end_time = date("H:i:s", $log[3]);
                        $window->save();
                    }
                } elseif (self::ACTION_SWITCH == (string)$log[2]) {
                
                    continue;
                    
                } else {
                    
                    throw new CException("Ошибка");//TODO:Описание доделать
                }
            }
            
        return true;
    }

    public static function getWindows($return) {

        $data['data'] = Yii::app()
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

        foreach ($data['data'] as  $k=>$row) {
            $data['data'][$k]['start'] = date("d.m.Y H:i:s", $data['data'][$k]['start']);
            $data['data'][$k]['end'] = date("d.m.Y H:i:s", $data['data'][$k]['end']);
            $data['data'][$k]['window'] = self::$screens[$data['data'][$k]['window']];
            $data['data'][$k]['sub_window'] = self::$subScreens[$data['data'][$k]['sub_window']];
        }
        $headers = array(
            'user_id'           => 'id_пользователя', 
            'email'             => 'email',
            'start'             => 'дата старта симуляции',
            'end'               => 'дата окончания симуляции',                
            'id'                => 'id_симуляции',
            'window'      => 'Активное окно',
            'sub_window'   => 'Активное подокно',
            'start_time'         => 'Игровое время - start',
            'end_time'           => 'Игровое время - end'                
        );
        if(self::RETURN_DATA == $return){
            $data['headers'] = $headers;
            $data['title'] = "Универсальное логирование";
            return $data;
        } elseif (self::RETURN_CSV == $return) {
        $csv = new ECSVExport($data['data'], true, true, ';');
        $csv->setHeaders($headers);
        $content = $csv->toCSVutf8BOM();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;
    }
}