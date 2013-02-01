<?php

class LogHelper {

    const ACTION_CLOSE = "0"; //Закрытие окна

    const ACTION_OPEN = "1"; //Открытие окна

    const ACTION_SWITCH = "2"; //Переход в рамках окна
    
    const ACTION_ACTIVATED = "activated"; //Активация окна
    
    const ACTION_DEACTIVATED = "deactivated"; //Деактивация окна

    const RETURN_DATA = 'json'; //Тип возвращаемого значения JSON

    const RETURN_CSV = 'csv'; //Тип возвращаемого значения CSV
    
    const LOGIN = false; //Писать лог в файл? true - да, false - нет
    
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
    
    const MAIL_MAIN = 'mail main';
    const MAIL_PREVIEW = 'mail preview';
    const MAIL_NEW = 'mail new';
    const MAIL_PLAN = 'mail plan';

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
    
    public static function getSubScreensArr(){
        return self::$subScreens;
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
                        $csv[] = gmdate("d.m.Y H:i:s", time()); //Дата и время на сервере
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
        for ($key = 0; $key < count($logs); $key++) {
            if(isset($logs[$key-1])){
                if(
                    $logs[$key][0] == $logs[$key-1][0] AND
                    $logs[$key][1] == $logs[$key-1][1] AND
                    $logs[$key][2] != $logs[$key-1][2] AND
                    $logs[$key][3] == $logs[$key-1][3] AND
                    (
                        count($logs[$key]) < 5 OR
                        (isset($logs[$key][4]) && isset($logs[$key-1][4]) && $logs[$key][4] == $logs[$key-1][4])
                    )
                ){
                    array_splice($logs, $key - 1, 2);
                    $key -= 2;
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
    public static function setLogDoialogPoint( $dialogId, $simId, $pointId ) {

		$comand = Yii::app()->db->createCommand();
		$comand->insert( "log_dialog_points" , array(
                         'sim_id'    => $simId,
                         'dialog_id' => $dialogId,
                         'point_id'  => $pointId
                        ));
	}

    public static function getDialogPointsDetail($return, $params=array()) {
        $sim_id = null;
        if (isset($params['sim_id'])) {
            $sim_id = $params['sim_id'];
        }
        
         $query = Yii::app()->db->createCommand()
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
            ->from('log_dialog_points l')
            ->join('characters_points c', 'l.point_id = c.point_id and l.dialog_id = c.dialog_id')
            ->join('dialogs d', 'd.id = c.dialog_id and d.id = l.dialog_id')
            ->join('characters_points_titles p', 'p.id = l.point_id and p.id = c.point_id')
            ->join('learning_goals p2', 'p2.code = p.learning_goal_code')
            ->leftJoin('type_scale t', 'p.type_scale = t.id')
            ->order('l.id');
         
        if (null !== $sim_id) {
            $query->where(" l.sim_id = {$sim_id} ");
        }
         
        $data['data'] = $query->queryAll();
        
        $mailQuery = Yii::app()->db->createCommand()
            ->select(' l.sim_id
                       , p2.code as p_code
                       , p2.title AS p_title
                       , p.code
                       , p.title
                       , t.value as type_scale
                       , p.scale
                       , mp.add_value
                       , l.full_coincidence
                       , l.part1_coincidence
                       , l.part2_coincidence
		')
            ->from('log_mail l')
            ->join('mail_box m', 'm.id = l.mail_id')
            ->join('mail_template mt', 'mt.code = m.code') // MS letetrs can has null template_id 
            ->join('mail_points mp', 'mt.id = mp.mail_id') // but we need MS template id to find mail points 
            ->join('characters_points_titles p', 'p.id = mp.point_id')
            ->join('learning_goals p2', 'p2.code = p.learning_goal_code')
            ->leftJoin('type_scale t', 'p.type_scale = t.id')
            ->order('l.id');
        
        if (null !== $sim_id) {
            $mailQuery->where(" l.sim_id = {$sim_id} AND m.group_id = 3 ");
        } else {
            $mailQuery->where('m.group_id = 3');
        }

        $mailLogData = $mailQuery->queryAll();
        
        // update mailLogData.out_mail_code {
        foreach ($mailLogData as $key => $logData) {
            if ('-' != $logData['full_coincidence']) {
                $mailLogData[$key]['out_mail_code'] = $logData['full_coincidence'];
            } elseif ('-' != $logData['part1_coincidence']) {
                $mailLogData[$key]['out_mail_code'] = $logData['part1_coincidence'];
            } elseif ('-' != $logData['part2_coincidence']) {
                $mailLogData[$key]['out_mail_code'] = $logData['part2_coincidence'];
            } else {
                $mailLogData[$key]['out_mail_code'] = '-';
                //unset($mailLogData[$key]);
            }
            
            $mailLogData[$key]['dialog_id'] = '-';
            $mailLogData[$key]['dialog_code'] = '-';
            $mailLogData[$key]['step_number'] = '-';
            $mailLogData[$key]['replica_number'] = '-';
            unset(
                $mailLogData[$key]['full_coincidence'],
                $mailLogData[$key]['part1_coincidence'],
                $mailLogData[$key]['part2_coincidence']
           );
            
           if ('-' == $mailLogData[$key]['out_mail_code']) {
               unset($mailLogData[$key]);
           }
        }
        // update mailLogData.out_mail_code }
        
        foreach ($data['data'] as  $k=>$row) {
            $data['data'][$k]['scale'] = Strings::toWin(str_replace('.', ',', $data['data'][$k]['scale']));
            $data['data'][$k]['out_mail_code'] = '-';
            $data['data'][$k]['add_value']  = str_replace('.', ',', $data['data'][$k]['add_value']);
            $data['data'][$k]['type_scale'] = str_replace('.', ',', $data['data'][$k]['type_scale']);
        }
        
        // merge dialog and mail logs
        $data['data'] = array_merge($mailLogData, $data['data']);
        
        $headers = array(
                'sim_id'         => 'id_симуляции',
                'p_code'         => 'Номер цели обучения',
                'p_title'        => 'Наименование цели обучения',
                'code'           => 'Номер поведения',
                'title'          => 'Наименование поведения',
                'type_scale'     => 'Тип поведения',
                'scale'          => 'Вес поведения',
                'add_value'      => 'Проявление',
                'dialog_id'      => 'Вызвавшая реплика (id_записи)',
                'dialog_code'    => 'Вызвавшая реплика (Код события)',
                'step_number'    => 'Вызвавшая реплика (номер шага)',
                'replica_number' => 'Вызвавшая реплика (номер реплики)',
                'out_mail_code'  => 'Вызвавшее исходящее письмо ',
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

     public static function getDialogPointsAggregate($return) {

            $data['data'] = Yii::app()->db->createCommand()
                ->select('l.sim_id,
                      p.code,
                      t.value as type_scale,
                      round(avg(c.add_value)*p.scale, 2) as avg')
                ->from('log_dialog_points l')
                ->leftJoin('characters_points c', 'l.dialog_id = c.dialog_id')
                ->leftJoin('characters_points_titles p', 'c.point_id = p.id')
                ->leftJoin('type_scale t', 'p.type_scale = t.id')
                ->group("l.sim_id, p.code")
                ->order("l.sim_id")
                ->queryAll();
            
            $headers = array(
                    'sim_id'     => 'id_симуляции',
                    'code'       => 'Номер поведения',
                    'type_scale' => 'Тип поведения',
                    'avg'        => 'Оценка по поведению'
            );
            
            // merge with email points (simulations_mail_points) {
            
            $emailPoints = Yii::app()->db->createCommand()
                ->select('smp.sim_id,
                      p.code,
                      t.value as type_scale,
                      smp.value as avg')
                ->from('simulations_mail_points smp')
                //->leftJoin('characters_points c', 'l.dialog_id = c.dialog_id')
                ->leftJoin('characters_points_titles p', 'smp.point_id = p.id')
                ->leftJoin('type_scale t', 'smp.scale_type_id = t.id')
                ->group("smp.sim_id, p.code")
                ->order("smp.sim_id")
                ->queryAll();
            
            $data['data'] = array_merge($data['data'], $emailPoints);
            
            foreach ($data['data'] as  $k=>$row) {
                $data['data'][$k]['avg'] = str_replace('.', ',', $data['data'][$k]['avg']);
            }
            
            // merge with email points }
            
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
        
     public static function getFullAgregatedLog($return) {
         
            $data['data'] = Yii::app()->db->createCommand()
                ->select('l.sim_id,
                      p.code,
                      t.value as type_scale,
                      l.value')
                ->from('assessment_aggregated l')
                ->leftJoin('characters_points_titles p', 'l.point_id = p.id')
                ->leftJoin('type_scale t', 'p.type_scale = t.id')
                ->group("l.sim_id, p.code")
                ->order("l.sim_id")
                ->queryAll();

            $headers = array(
                    'sim_id'     => 'id_симуляции',
                    'code'       => 'Номер поведения',
                    'type_scale' => 'Тип поведения',
                    'value'        => 'Оценка по поведению'
            );
            
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]['value']      = str_replace('.', ',', $value['value']);
                $data['data'][$key]['type_scale'] = str_replace('.', ',', $value['type_scale']);
            }
            
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

        foreach( $logs as $log ) {
                
            if( in_array( $log[0], self::$codes_documents ) || in_array( $log[1], self::$codes_documents ) ) {

                if(!isset($log[4]['fileId'])) continue;
                
                if( self::ACTION_OPEN == (string)$log[2] OR self::ACTION_ACTIVATED == (string)$log[2]){

                    $log_obj = new LogDocuments();
                    $log_obj->sim_id=$simId;
                    $log_obj->file_id = $log[4]['fileId'];
                    $log_obj->start_time=gmdate("H:i:s", $log[3]);
                    $log_obj->save();
                } elseif( self::ACTION_CLOSE == (string)$log[2] OR self::ACTION_DEACTIVATED == (string)$log[2]) {
                    
                    $log_obj = LogDocuments::model()->findByAttributes(array(
                        'file_id' => $log[4]['fileId'],
                        'end_time' => '00:00:00',
                        'sim_id' => $simId
                    ));
                    if(!$log_obj) continue;
                    $log_obj->end_time = gmdate("H:i:s", $log[3]);
                    $log_obj->save();
                    continue;    
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
    
    public static function setMailLog( $simId, $logs ) 
    {
        if (!is_array($logs)) return false;
        foreach( $logs as $log ) {
            
            if( in_array( $log[0], self::$codes_mail ) || in_array( $log[1], self::$codes_mail ) ) {
                
                if( self::ACTION_OPEN == (string)$log[2] OR self::ACTION_ACTIVATED == (string)$log[2] ) {
                    $log_obj = new LogMail();
                    $log_obj->sim_id = $simId;
                    $log_obj->mail_id = empty($log[4]['mailId']) ? NULL : $log[4]['mailId'];
                    $log_obj->window = $log[1];
                    $log_obj->start_time = gmdate("H:i:s", $log[3]);
                    $log_obj->save();
                    continue;
                    
                } elseif( self::ACTION_CLOSE == (string)$log[2] OR self::ACTION_DEACTIVATED == (string)$log[2] ) {
                    if (false === isset($log[4]) || false === isset($log[4]['planId'])) {
                        $log[4]['planId'] = null;
                    }
                    
                    if($log[1] != 13) {
                        // reply, or close mail-plan, or close mail-main
                        $log_obj = LogMail::model()->findByAttributes(array(
                            'mail_id' => empty($log[4]['mailId']) ? NULL : $log[4]['mailId'], //todo:Добавлена проверка, хотя mailId должен быть в данной ситуации всегда
                            'end_time' => '00:00:00',
                            'sim_id' => $simId
                        ));
                        if(!$log_obj) continue;
                        $log_obj->end_time = gmdate("H:i:s", $log[3]);
                        $log_obj->mail_task_id = $log[4]['planId'];
                        $log_obj->save();
                        continue;
                        
                    } else {
                        // new mail
                        
                        // check MS email concidence with mail_templates { 
                        $result = array(
                            'full'           => '-',
                            'part1'          => '-',
                            'part2'          => '-',
                            'has_concidence' => 0,
                        );
                        
                        if (isset($log[4]) && isset($log[4]['mailId'])) {
                            $result = MailBoxService::updateMsCoincidernce($log[4]['mailId'], $simId);
                        }
                        // check MS email concidence with mail_templates }
                        $log_obj = LogMail::model()->findByAttributes(array(
                            "mail_id" => null,
                            "end_time" => '00:00:00',
                            "sim_id" => $simId
                        ));
                        if(!$log_obj) continue;
                        $log_obj->end_time = gmdate("H:i:s", $log[3]);
                        $log_obj->mail_task_id = $log[4]['planId'];
                        $log_obj->mail_id  = empty($log[4]['mailId'])?NULL:$log[4]['mailId'];
                        $log_obj->full_coincidence  = $result['full'];
                        $log_obj->part1_coincidence = $result['part1'];
                        $log_obj->part2_coincidence = $result['part2'];
                        $log_obj->is_coincidence = $result['has_concidence'];
                        $log_obj->save();
                        continue;
                         
                    }
                    
                } elseif( self::ACTION_SWITCH == (string)$log[2] ) {
                    $log_obj = LogMail::model()->findByAttributes(array(
                        'end_time' => '00:00:00',
                        'sim_id' => $simId
                    ));
                    if(!$log_obj) continue;
                    $log_obj->end_time = gmdate( "H:i:s", $log[3] );
                    $log_obj->save();

                    $log_obj = new LogMail();
                    $log_obj->sim_id = $simId;
                    $log_obj->mail_id = $log[4]['mailId'];
                    $log_obj->window = $log[1];
                    $log_obj->start_time = gmdate("H:i:s", $log[3]);
                    $log_obj->save();

                } else {
                    throw new Exception("Ошибка"); //TODO:Описание доделать
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
                    , mt.type_of_importance
                    , if(m.readed = 0, 'Нет', 'Да') AS readed
                    , if(m.plan = 0, 'Нет', 'Да') AS plan
                    , if(m.reply = 0, 'Нет', 'Да') AS reply
                    , m.id
                    ")
            ->from('mail_box m')
            ->join('mail_group g', 'm.group_id = g.id')
            ->join('mail_template mt', 'm.code = mt.code')
            ->where('m.type = 1 or m.type = 3')
            ->order('m.id')
            ->queryAll();
        
        // add is right mail_task planned  {
        $logMail = array();
        foreach (LogMail::model()->byWindow(14)->findAll() as $log) {
            $logMail[$log->mail_id] = $log;
        }
        
        $mailTask = array();
        foreach (MailTasksModel::model()->findAll() as $line) {
            $mailTask[$line->id] = $line;
        }
        
        foreach ($data['data'] as $key => $value) {
            $data['data'][$key]['mail_task_is_correct'] = '-';
            
            if ('Да' === $value['plan'] && 'plan' !== $value['type_of_importance']) {
                $data['data'][$key]['mail_task_is_correct'] = 'W';
            }
            
            if (isset($logMail[$value['id']])) {
                $mailTaskId = $logMail[$value['id']]->mail_task_id;
                if (null !== $mailTaskId) {
                    $data['data'][$key]['mail_task_is_correct'] = $mailTask[$mailTaskId]->wr;
                }                
            }
        }
        // add is right mail_task planned  }

        $headers = array(
            'sim_id'                 => 'id_симуляции',
            'code'                   => 'Код входящего письма',
            'name'                   => 'Папка мейл-клиента',
            'type_of_importance'     => 'Тип письма',
            'readed'                 => 'Письмо прочтено (да/нет)',
            'plan'                   => 'Письмо запланировано (да/нет)',
            'reply'                  => 'На письмо отправлен ответ',
            'mail_task_is_correct'   => 'Задача запланирована правильно?',
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
                , ifnull(s.text, '-') AS subject
                , ifnull(t.code, '-') AS code
                , l.full_coincidence
                , l.part1_coincidence
                , l.part2_coincidence
                , if(l.is_coincidence = 1, 'Да', 'Нет') AS is_coincidence
                ")
            ->from('log_mail l')
            ->leftJoin('mail_box m', 'l.mail_id = m.id')
            ->leftJoin('mail_receivers r', 'l.mail_id = r.mail_id')
            ->leftJoin('mail_copies c', 'l.mail_id = c.mail_id')
            ->leftJoin('mail_attachments a', 'm.id = a.mail_id')
            ->leftJoin('my_documents d', 'a.file_id = d.id')
            ->leftJoin('my_documents_template t', 'd.template_id = t.id')
            ->leftJoin(CommunicationTheme::model()->tableName() . ' s', 'm.subject_id = s.id')
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
            'code'   => 'Код вложения',
            'full_coincidence'   => 'Полное совпадение',
            'part1_coincidence'   => 'Совпадение 1',
            'part2_coincidence'   => 'Совпадение 2',
            'is_coincidence'   => 'Есть совпадение?',
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

                if( self::ACTION_OPEN == (string)$log[2] || self::ACTION_ACTIVATED == (string)$log[2]) {
                    if (LogWindows::model()->countByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simId))) {
                        throw(new CException('Previous window is still activated'));
                    }
                    $log_window = new LogWindows();
                    $log_window->sim_id = $simId;
                    $log_window->window = $log[0];
                    $log_window->sub_window = $log[1];
                    $log_window->start_time  = gmdate("H:i:s", $log[3]);
                    $log_window->save();
                    continue;
                    
                } elseif( self::ACTION_CLOSE == (string)$log[2] || self::ACTION_DEACTIVATED == (string)$log[2] ) {
                    $windows = LogWindows::model()->findAllByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simId));
                    if (0 == count($windows)) {
                        throw(new CException('No active windows. Achtung!'.$simId));
                    }
                    if (1 < count($windows)) {
                        throw(new CException('Two or more active windows at one time. Achtung!'));
                    }
                    foreach ($windows as $window) {
                        $window->end_time = gmdate("H:i:s", $log[3]);
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
    
    public static function getDayPlan($return) {

        $sql = "SELECT 
                    d.sim_id,
                    CONCAT('день ', d.day) AS day,
                    if (d.snapshot_time = 1, '11:00', '18:00') AS snapshot_time,
                    t.code,
                    t.title,
                    t.category,
                    d.date,
                    'нет' as is_done,
                    d.todo_count
                FROM day_plan_log as d
                left join tasks as t on (t.id = d.task_id)";
        
        $data['data'] = Yii::app()->db->createCommand($sql)->queryAll();
        
        $headers = array(
            'sim_id'        => 'id_симуляции', 
            'day'           => 'Графа плана',
            'snapshot_time' => 'Время логирования состояния плана',
            'code'          => 'Код задачи',                
            'title'         => 'Наименование задачи',
            'category'      => 'Категория задачи',
            'date'          => 'Время, на которое стоит в плане',
            'is_done'       => 'Сделана ли задача',
            'todo_count'    => 'Кол-во задач в "Сделать"'             
        );
        if(self::RETURN_DATA == $return){
            $data['headers'] = $headers;
            $data['title'] = "Логирование работы с планом";
            return $data;
        } elseif (self::RETURN_CSV == $return) {
        $csv = new ECSVExport($data['data'], true, true, ';');
        $csv->setHeaders($headers);
        $csv->setCallback(function($row){
            switch ($row['day']) {
                case 1:
                    $row['day'] = 'today';
                    break;

                case 2:
                    $row['day'] = 'tomorrow';
                    break;
                
                case 3:
                    $row['day'] = 'after vacation';    
                    $row['date'] = 'any';
                    break;
            }
            
            //$row['snapshot_time'] = DateHelper::toString($row['snapshot_time']);
            if ($row['snapshot_time'] == 1) $row['snapshot_time'] = '11:00';
            else $row['snapshot_time'] = 'end';
             
            $row['is_done'] = 'no';
            
            return $row;
        });
        $content = $csv->toCSVutf8BOM();
        $filename = 'data.csv';
        Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        return true;
    }

    public static function setDialogs($simId, $logs){
        if (!is_array($logs)) return false;

        foreach( $logs as $log ) {
            if (empty($log[4]['dialogId'])) continue;
            
            $lastDialogIdInMySQL = isset($log[4]['lastDialogId']) ? $log[4]['lastDialogId'] : null;
            $last_dialog = Dialogs::model()->findByAttributes(['id' => $lastDialogIdInMySQL, 'is_final_replica' => 1]);
            $lastDialogIdAccordingExcel = (null === $last_dialog) ? null : $last_dialog->excel_id;
            
            if( self::ACTION_OPEN == (string)$log[2] || self::ACTION_ACTIVATED == (string)$log[2]) {


                $last_dialog = new LogDialogs();
                $last_dialog->sim_id = $simId;
                $last_dialog->dialog_id = $log[4]['dialogId'];
                $last_dialog->last_id = $lastDialogIdAccordingExcel;
                $last_dialog->start_time  = gmdate("H:i:s", $log[3]);
                $last_dialog->save();
                continue;

            } elseif( self::ACTION_CLOSE == (string)$log[2] || self::ACTION_DEACTIVATED == (string)$log[2] ) {
                $dialogs = LogDialogs::model()->findAllByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simId, 'dialog_id' => $log[4]['dialogId']));
                if (!$dialogs) {
                    continue;
                }
                foreach ($dialogs as $last_dialog) {
                    $last_dialog->end_time = gmdate("H:i:s", $log[3]);
                    $last_dialog->last_id = $lastDialogIdAccordingExcel;
                    $last_dialog->save();
                }
            } elseif (self::ACTION_SWITCH == (string)$log[2]) {

                continue;

            } else {

                throw new CException("Ошибка");//TODO:Описание доделать
            }
        }

        return true;
    }
    
    public static function getDialogs($return) {

            $data['data'] = Yii::app()->db->createCommand()
                ->select("l.sim_id, 
                    d.code as code, 
                    s.title as category,
                    if(d.type_of_init != 'flex', 'System_dial', 'Manual_dial') as type_of_init,
                    l.last_id, 
                    l.start_time, 
                    l.end_time")
                ->from('log_dialogs l')
                ->leftJoin('dialogs d', 'l.dialog_id = d.id')
                ->leftJoin('dialog_subtypes s', 'd.dialog_subtype = s.id')
                ->order("l.id")
                ->queryAll();

            $data['headers'] = array(
                    'sim_id'     => 'id_симуляции',
                    'code'       => 'Код события',
                    'category'   => 'Категория события',
                    'type_of_init'   => 'Категория события',
                    'last_id'    => 'Результирующее id_записи',
                    'start_time' => 'Игровое время - start',
                    'end_time'   => 'Игровое время - end'
            );
            
            if(self::RETURN_DATA == $return) {
                $data['title'] = "Логирование работы с Документами";
                return $data;
            } elseif (self::RETURN_CSV == $return) {
                $csv = new ECSVExport($data['data'], true, true, ';');
                $csv->setHeaders($data['headers']);
                $content = $csv->toCSVutf8BOM();
                $filename = 'data.csv';
                Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
            } else {
                throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
            }
         return true;
    }
    
    public static function getLegActionsDetail($return, $simulation = NULL, $isUnsetData = true) {
        $simSql = '';
        if (NULL !== $simulation) {
           $simSql = sprintf(' WHERE l.sim_id = %s ', $simulation->id); 
        }
        
        $sql = "SELECT DISTINCT
                            l.sim_id
                          , '' as leg_type
                          , '' as leg_action
                          , l.mail_id
                          , d.code as dialog_code
                          , x.code as mail_code
                          , t.code as doc_code
                          , a.window_id
                          , w.subtype
                          , d.type_of_init
                          , a.dialog_id
                          , x.group_id
                          , x.coincidence_mail_code
                          , i.category_id AS category
                          , if(a.is_keep_last_category = 0, 'yes', '') AS is_keep_last_category
                          , l.start_time AS start_time
                          , ifnull(l.end_time, '00:00:00') AS end_time
                          , timediff(ifnull(l.end_time, '00:00:00'), l.start_time) AS diff_time,
                          l.activity_action_id,
                          i.category_id,
                          a.is_keep_last_category,
                          a.activity_id
            FROM
              log_activity_action AS l
            LEFT JOIN activity_action AS a
            ON l.activity_action_id = a.id
            LEFT JOIN window AS w
            ON w.id = a.window_id
            LEFT JOIN dialogs AS d
            ON d.id = a.dialog_id
            LEFT JOIN my_documents_template AS t
            ON t.id = a.document_id
            LEFT JOIN activity AS i
            ON i.id = a.activity_id
            LEFT JOIN mail_box AS x
            ON l.mail_id = x.id AND l.sim_id = x.sim_id
            {$simSql}
            ORDER BY
              l.id";

        $data['data'] = Yii::app()->db->createCommand($sql)->queryAll();
            foreach($data['data'] as $k => $v) {
                
                if( !empty($data['data'][$k]['dialog_id']) ){
                    if( $data['data'][$k]['type_of_init'] !== 'flex' ) {
                        $data['data'][$k]['leg_type'] = 'System_dial_leg';
                    } else {
                        $data['data'][$k]['leg_type'] = 'Manual_dial_leg'; }
                } elseif ( !empty($data['data'][$k]['mail_id']) ){
                    //Logger::write($data['data'][$k]['group_id']);
                    if($data['data'][$k]['group_id'] == 1) {
                        $data['data'][$k]['leg_type'] = 'Inbox_leg';
                    }else{
                        $data['data'][$k]['leg_type'] = 'Outbox_leg';
                    }
                    //Logger::write($data['data'][$k]['leg_type']);
                } else if(!empty($data['data'][$k]['document_id'])){
                    $data['data'][$k]['leg_type'] = 'Documents_leg';
                } else if(!empty($data['data'][$k]['window_id'])){
                    $data['data'][$k]['leg_type'] = 'Window';
                }

                if( !empty($data['data'][$k]['dialog_id']) ){
                    $data['data'][$k]['leg_action'] = $data['data'][$k]['dialog_code'];
                } elseif ( !empty($data['data'][$k]['mail_id']) ){
                    if($data['data'][$k]['group_id'] == 1) {
                        $data['data'][$k]['leg_action'] = $data['data'][$k]['mail_code'];
                    }else if($data['data'][$k]['group_id'] == 3){
                        if(empty($data['data'][$k]['coincidence_mail_code'])) {
                            $data['data'][$k]['leg_action'] = 'incorrect_sent';
                        } else {
                            $data['data'][$k]['leg_action'] = $data['data'][$k]['coincidence_mail_code'];
                        }
                    } else {
                        $data['data'][$k]['leg_action'] = 'not_sent';
                    }
                } else if(!empty($data['data'][$k]['document_id'])){
                    $data['data'][$k]['leg_action'] = $data['data'][$k]['doc_code'];
                } else if(!empty($data['data'][$k]['window_id'])){
                    $data['data'][$k]['leg_action'] = $data['data'][$k]['subtype'];
                }



                if($data['data'][$k]['group_id'] == 3 AND empty($data['data'][$k]['coincidence_mail_code'])){

                }else{
                    $data['data'][$k]['mail_id'] = '';
                }

                if ($isUnsetData) {
                    unset($data['data'][$k]['coincidence_mail_code']);
                    unset($data['data'][$k]['activity_action_id']);
                    unset($data['data'][$k]['category_id']);
                    unset($data['data'][$k]['activity_id']);
                    unset($data['data'][$k]['group_id']);
                    unset($data['data'][$k]['dialog_id']);
                    unset($data['data'][$k]['type_of_init']);
                    unset($data['data'][$k]['document_id']);
                    unset($data['data'][$k]['window_id']);
                }
            }

            $data['headers'] = array(
                'sim_id' => 'id_симуляции',
                'leg_type' => 'Leg_type',
                'leg_action' => 'Leg_action',
                'mail_id'=>'id_исходящего письма',
                'category' => 'Category',
                'is_keep_last_category' => 'Keep last category',
                'start_time' => 'Игровое время - start',
                'end_time' => 'Игровое время - end',
                'diff_time' => 'Разница времени');
            
            if(self::RETURN_DATA == $return) {
                $data['title'] = "Логирование Leg_actions - detail";
                return $data;
            } elseif (self::RETURN_CSV == $return) {
                $csv = new ECSVExport($data['data'], true, true, ';');
                $csv->setHeaders($data['headers']);
                $content = $csv->toCSVutf8BOM();
                $filename = 'data.csv';
                Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
            } else {
                throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
            }
         return true;
    }
    
    /**
     * @param string $return
     * 
     * @return string|boolean
     * 
     * @throws Exception
     */                    
    public static function getExcelAssessmentDetail($return)
    {
        $sql = 'SELECT 
            ep.sim_id,
            ep.formula_id,
            ep.value,
            f.formula
            FROM simulations_excel_points AS ep
            LEFT JOIN excel_points_formula AS f ON f.id = ep.formula_id
            ORDER BY ep.sim_id DESC;';
        
        $data['data'] = Yii::app()->db->createCommand($sql)->queryAll();
        
        foreach ($data['data'] as $key => $line) {
            // "convert" excel fopmulas to excel string, by adding space before =
            $data['data'][$key]['formula'] = ' '. $line['formula']; 
            $data['data'][$key]['value']   = $line['value']; 
        }
        
        $data['headers'] = array(
            'sim_id'       => 'ID симуляции',
            'formula_id'   => 'ID формулы',
            'value'        => 'Правильный результат?',
            'formula'      => 'Формула'
            );

        if(self::RETURN_DATA == $return) {
            $data['title'] = "Логирование Leg_actions - detail";
            return $data;
        } elseif (self::RETURN_CSV == $return) {
            $csv = new ECSVExport($data['data'], true, true, ';');
            $csv->setHeaders($data['headers']);
            $content = $csv->toCSVutf8BOM();
            $filename = 'data.csv';
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
        }
        
        return true;
    }
    
    public static function fixLogWhenSimStop($simulation)
    {
        self::fixUniversalLogWindocOpenAndCloseTime($simulation);
    }
    
    public static function fixUniversalLogWindocOpenAndCloseTime($simulation) {
        $previouseItem = null;
        
        $list = LogWindows::model()->findAll([
            'order'     => 'start_time ASC', 
            'condition' => 'sim_id=:sim_id', 
            'params'    => [ ':sim_id'=>$simulation->id ]
        ]);
        
        $connection  = Yii::app()->db;
        $transaction = $connection->beginTransaction(); // to avoid many single UPDATEs
        
        foreach ($list as $logWindowsItem) {
            if (null !== $previouseItem) {
                if ($logWindowsItem->start_time !== $previouseItem->end_time) {
                    $logWindowsItem->start_time = $previouseItem->end_time;
                    $logWindowsItem->save();
                }
            }
            
            $previouseItem = $logWindowsItem;
        }
        
        $transaction->commit();
    }

    /**
     * 
     * @param Simulations $simulation
     */
    public static function combineLogActivityAgregated($simulation) {
        $agregatedActivity = NULL;
        
        $data = self::getLegActionsDetail(self::RETURN_DATA, $simulation, false);

        foreach($data['data'] as $activityAction) {
            if (NULL === $agregatedActivity) {
                // init new agregatedActivity at first iteration
                $agregatedActivity = new LogActivityActionAgregated();
                
                $agregatedActivity->sim_id                = $simulation->id;
                $agregatedActivity->leg_type              = $activityAction['leg_type'];                
                $agregatedActivity->leg_action            = $activityAction['leg_action'];
                $agregatedActivity->activityAction        = ActivityAction::model()->findByPk($activityAction['activity_action_id']);
                $agregatedActivity->category              = $activityAction['category_id'];
                $agregatedActivity->is_keep_last_category = $activityAction['is_keep_last_category'];
                $agregatedActivity->start_time            = $activityAction['start_time'];
                $agregatedActivity->end_time              = $activityAction['end_time'];
                $agregatedActivity->duration              = $activityAction['diff_time'];
            } else {
                // IF previouse action activity the same with current 
                // OR current activity action duration < 10 real seconds
                // THEN prolong previouse actvity 
                $actionDurationInGameSeconds = TimeTools::TimeToSeconds($activityAction['diff_time']);
                $limit = Yii::app()->params['public']['skiliksSpeedFactor']*10; // 10 real seconds
                if ( $agregatedActivity->activityAction->activity_id === $activityAction['activity_id'] ||
                    $actionDurationInGameSeconds < $limit ) {
                    // prolong previouse activity :
                    $agregatedActivity->end_time = $activityAction['end_time']; 
                    $agregatedActivity->updateDuration();
                } else {
                    // activity and, save it
                    $agregatedActivity->save();
                    
                    // init new agregatedActivity for new activity
                    $agregatedActivity = new LogActivityActionAgregated();

                    $agregatedActivity->sim_id                = $simulation->id;
                    $agregatedActivity->leg_type              = $activityAction['leg_type'];                
                    $agregatedActivity->leg_action            = $activityAction['leg_action'];
                    $agregatedActivity->activityAction        = ActivityAction::model()->findByPk($activityAction['activity_action_id']);
                    $agregatedActivity->category              = $activityAction['category_id'];
                    $agregatedActivity->is_keep_last_category = $activityAction['is_keep_last_category'];
                    $agregatedActivity->start_time            = $activityAction['start_time'];
                    $agregatedActivity->end_time              = $activityAction['end_time'];
                    $agregatedActivity->duration              = $activityAction['diff_time'];
                }
            }
        }
        
        if (NULL !== $agregatedActivity) {
            $agregatedActivity->save();
        }
    }
    
    public static function getLegActionsAgregated($return, $simulation = NULL) {
        $simSql = '';
        if (NULL !== $simulation) {
           $simSql = sprintf(' WHERE l.sim_id = %s ', $simulation->id); 
        }
        
        $sql = "SELECT
                l.sim_id
              , l.leg_type
              , l.leg_action
              , l.category
              , if(l.is_keep_last_category = 0, 'yes', '') AS is_keep_last_category
              , l.start_time AS start_time
              , l.end_time
              , l.duration
            FROM
              log_activity_action_agregated AS l
            {$simSql}
            ORDER BY
              l.id";

        $data['data'] = Yii::app()->db->createCommand($sql)->queryAll();

            $data['headers'] = array(
                'sim_id'                => 'id_симуляции',
                'leg_type'              => 'Leg_type',
                'leg_action'            => 'Leg_action',
                'category'              => 'Category',
                'is_keep_last_category' => 'Keep last category',
                'start_time'            => 'Игровое время - start',
                'end_time'              => 'Игровое время - end',
                'duration'              => 'Разница времени');
            
            if(self::RETURN_DATA == $return) {
                $data['title'] = "Логирование Leg_actions - agregated";
                return $data;
            } elseif (self::RETURN_CSV == $return) {
                $csv = new ECSVExport($data['data'], true, true, ';');
                $csv->setHeaders($data['headers']);
                $content = $csv->toCSVutf8BOM();
                $filename = 'data.csv';
                Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
            } else {
                throw new Exception('Не верный параметр $return = '.$return.' метода '.__CLASS__.'::'.__METHOD__);
            }
         return true;
    }
}