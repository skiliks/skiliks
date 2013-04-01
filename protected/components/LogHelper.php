<?php

class LogHelper
{
    const ACTION_CLOSE = "0"; //Закрытие окна
    const ACTION_OPEN = "1"; //Открытие окна
    const ACTION_SWITCH = "2"; //Переход в рамках окна

    const ACTION_ACTIVATED = "activated"; //Активация окна
    const ACTION_DEACTIVATED = "deactivated"; //Деактивация окна

    const RETURN_DATA = 'json'; //Тип возвращаемого значения JSON
    const RETURN_CSV = 'csv'; //Тип возвращаемого значения CSV

    const LOGIN = false; //Писать лог в файл? true - да, false - нет

    public $bom = "0xEF 0xBB 0xBF";

    protected static $codes_documents = array(40, 41, 42);

    protected static $codes_mail = array(10, 11, 12, 13, 14);

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

    const MAIL_NEW_WINDOW_TYPE_ID = 13;

    protected static $subScreens = array(
        1 =>  'main screen',
        3 =>  'plan',
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
        0 => 'close',
        1 => 'open',
        2 => 'switch',
        'activated' => 'activated',
        'deactivated' => 'deactivated',
    );

    private function __construct()
    {

    }

    public static function getSubScreensArr()
    {
        return self::$subScreens;
    }


    public static function setLog($simId, $logs)
    {

        if (self::LOGIN) {
            if (is_array($logs)) {
                $sparator = ';';
                $end = "\r\n";
                if (is_dir(__DIR__ . '/../runtime/')) {
                    $file = fopen(__DIR__ . '/../runtime/windows.log', "a+");
                    foreach ($logs as $log) {
                        $hours = floor($log[3] / 3600);
                        $minutes = floor($log[3] / 60) - $hours * 60;
                        $seconds = $log[3] - 3600 * $hours - 60 * $minutes;

                        $csv = array();
                        $csv[] = gmdate("d.m.Y H:i:s", time()); //Дата и время на сервере
                        $csv[] = $simId; //id симуляции
                        $csv[] = $log[0]; //Активное окно
                        $csv[] = $log[1]; //Активное под окно
                        $csv[] = $log[2]; //Действие
                        $csv[] = $log[3]; //Игровое время
                        $csv[] = (empty($log[4]['mailId']) ? '' : $log[4]['mailId']); // Дополнительный параметр mailId
                        $csv[] = (empty($log[4]['fileId']) ? '' : $log[4]['fileId']); // Дополнительный параметр fileId
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
                        fwrite($file, implode($sparator, $csv) . $end);
                    }
                    fclose($file);
                } else {
                    throw new Exception("Не правильный путь " . __DIR__ . '/../runtime/');
                }
            }
        }

    }

    public static function logFilter($logs)
    {

        if (!is_array($logs)) return false;
        for ($key = 0; $key < count($logs); $key++) {
            if (isset($logs[$key - 1])) {
                if (
                    $logs[$key][0] == $logs[$key - 1][0] AND
                    $logs[$key][1] == $logs[$key - 1][1] AND
                    $logs[$key][2] != $logs[$key - 1][2] AND
                    $logs[$key][3] == $logs[$key - 1][3] AND
                    (
                        count($logs[$key]) < 5 OR
                        (isset($logs[$key][4]) && isset($logs[$key - 1][4]) && $logs[$key][4] == $logs[$key - 1][4])
                    )
                ) {
                    array_splice($logs, $key - 1, 2);
                    $key -= 2;
                } else {
                    continue;
                }
            } else {
                continue;
            }
        }
        return $logs;
    }

    private static function order($order_col, $columns, $order_type = "asc")
    {
        if (is_array($columns)) {

        } else {
            throw new Exception('Параметр $columns не задан!');
        }
        if (in_array($order_type, array('asc', 'desc'))) {
            return "{$order_col} {$order_type}";
        } else {
            throw new Exception("Тип сортировки '$order_type' неизвестен!");
        }
    }

    /**
     * Пишет лог для Логирование расчета оценки - детально
     * @param int $dialogId ID - диалог
     * @param int $simId ID    - Симуляция
     * @param ReplicaPoint $pointId ID  - Поинт с таблицы `characters_points_titles`
     */
    public static function setDialogPoint($dialogId, $simId, $point)
    {
        $log = new AssessmentPoint();
        $log->sim_id = $simId;
        $log->dialog_id = $dialogId;
        $log->point_id = $point->point_id;
        $log->value = $point->add_value;

        $log->save();
    }

    /**
     * @param Replica $replica
     * @param Simulation $simulation
     */
    public static function setReplicaLog($replica, $simulation) {
        $log = new LogReplica();
        $log->sim_id = $simulation->id;
        $log->replica_id = $replica->id;
        $log->time = $simulation->getGameTime();

        $log->save();
    }

    /**
     * Piece of code, which returns mail points
     *
     * @param $return
     * @param array $params
     * @return array
     * @throws Exception
     */
    public static function getMailPointsDetail($return, $params = array())
    {
        $sim_id = null;
        if (isset($params['sim_id'])) {
            $sim_id = $params['sim_id'];
        }

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
            ->join('hero_behaviour p', 'p.id = mp.point_id')
            ->join('learning_goal p2', 'p2.code = p.learning_goal_code')
            ->leftJoin('type_scale t', 'p.type_scale = t.id')
            ->order('l.id');

        if (null !== $sim_id) {
            $mailQuery->where(" l.sim_id = {$sim_id} AND m.group_id = 3 ");
        } else {
            $mailQuery->where('m.group_id = 3');
        }

        $data['data'] = $mailQuery->queryAll();

        foreach ($data['data'] as  &$logData) {
            if ('-' != $logData['full_coincidence']) {
                $logData['out_mail_code'] = $logData['full_coincidence'];
            } elseif ('-' != $logData['part1_coincidence']) {
                $logData['out_mail_code'] = $logData['part1_coincidence'];
            } elseif ('-' != $logData['part2_coincidence']) {
                $logData['out_mail_code'] = $logData['part2_coincidence'];
            } else {
                $logData['out_mail_code'] = '-';
            }

            unset(
                $logData['full_coincidence'],
                $logData['part1_coincidence'],
                $logData['part2_coincidence']
            );

            if ('-' == $logData['out_mail_code']) {
                unset($logData);
            }
        }

        $headers = array(
            'sim_id' => 'id_симуляции',
            'p_code' => 'Номер цели обучения',
            'p_title' => 'Наименование цели обучения',
            'code' => 'Номер поведения',
            'title' => 'Наименование поведения',
            'type_scale' => 'Тип поведения',
            'scale' => 'Вес поведения',
            'add_value' => 'Проявление',
            'out_mail_code' => 'Вызвавшее исходящее письмо ',
        );

        if (self::RETURN_DATA == $return) {
            $data['headers'] = $headers;
            $data['title'] = "Логирование расчета оценки писем - детально";
            return $data;
        } elseif (self::RETURN_CSV == $return) {
            $csv = new ECSVExport($data['data'], true, true, ';');
            $csv->setHeaders($headers);
            $content = $csv->toCSVutf8BOM();
            $filename = 'assesment_mail_detailed.csv';
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = ' . $return . ' метода ' . __CLASS__ . '::' . __METHOD__);
        }

        return true;
    }


    public static function setDocumentsLog($simId, $logs)
    {

        if (!is_array($logs)) return false;

        foreach ($logs as $log) {

            if (in_array($log[0], self::$codes_documents) || in_array($log[1], self::$codes_documents)) {

                if (!isset($log[4]['fileId'])) continue;

                if (self::ACTION_OPEN == (string)$log[2] OR self::ACTION_ACTIVATED == (string)$log[2]) {

                    $log_obj = new LogDocument();
                    $log_obj->sim_id = $simId;
                    $log_obj->file_id = $log[4]['fileId'];
                    $log_obj->start_time = gmdate("H:i:s", $log[3]);
                    $log_obj->save();
                } elseif (self::ACTION_CLOSE == (string)$log[2] OR self::ACTION_DEACTIVATED == (string)$log[2]) {

                    $log_obj = LogDocument::model()->findByAttributes(array(
                        'file_id'  => $log[4]['fileId'],
                        'end_time' => '00:00:00',
                        'sim_id'   => $simId
                    ));
                    if (!$log_obj) continue;
                    $log_obj->end_time = gmdate("H:i:s", $log[3]);
                    $log_obj->save();
                    continue;
                } else {
                    throw new Exception("Ошибка"); //TODO:Описание доделать
                }
            }
        }

        return true;
    }


    public static function setMailLog($simId, $logs)
    {
        if (!is_array($logs)) return false;
        foreach ($logs as $log) {

            if (in_array($log[0], self::$codes_mail) || in_array($log[1], self::$codes_mail)) {
                assert($log['window_uid']);
                if (self::ACTION_OPEN == (string)$log[2] OR self::ACTION_ACTIVATED == (string)$log[2]) {
                    if ((int)$log[1] === 11 && empty($log[4]['mailId'])) { #Opening mail main does not add to mail log!
                        continue;
                    }
                    $log_obj = new LogMail();
                    $log_obj->sim_id = $simId;
                    $log_obj->mail_id = empty($log[4]['mailId']) ? NULL : $log[4]['mailId'];
                    $log_obj->window = $log[1];
                    $log_obj->start_time = gmdate("H:i:s", $log[3]);
                    $log_obj->window_uid = (isset($log['window_uid'])) ? $log['window_uid'] : NULL;
                    $log_obj->save();
                    continue;

                } elseif (self::ACTION_CLOSE == (string)$log[2] OR self::ACTION_DEACTIVATED == (string)$log[2]) {
                    if (false === isset($log[4]) || false === isset($log[4]['planId'])) {
                        $log[4]['planId'] = null;
                    }

                    if ($log[1] != 13) {
                        // reply, or close mail-plan, or close mail-main
                        $log_obj = LogMail::model()->findByAttributes(array(
                            //todo:Добавлена проверка, хотя mailId должен быть в данной ситуации всегда
                            // в случае закрыния окна mailMain из пустой папки "Корзина" например mailId нет.
                            'mail_id'    => empty($log[4]['mailId']) ? NULL : $log[4]['mailId'],
                            'end_time'   => '00:00:00',
                            'sim_id'     => $simId
                        ));
                        if (!$log_obj) continue;
                        $log_obj->end_time = gmdate("H:i:s", $log[3]);
                        $log_obj->mail_task_id = $log[4]['planId'];
                        $log_obj->save();
                        continue;

                    } else {
                        // new mail

                        // check MS email concidence with mail_templates { 
                        $result = array(
                            'full' => '-',
                            'part1' => '-',
                            'part2' => '-',
                            'has_concidence' => 0,
                        );
                        
                        if (isset($log[4]) && isset($log[4]['mailId'])) {
                            $result = MailBoxService::updateMsCoincidence($log[4]['mailId'], $simId);
                        }

                        if (false == isset($log['window_uid'])) {
                            throw new Exception('Window id isn`t set for mail  with ID '.$log[4]['planId'].';');
                        }
                        // check MS email concidence with mail_templates }
                        /** @var $log_obj LogMail */
                        $log_objs = LogMail::model()->findAllByAttributes(array(
                            //"mail_id"    => null,
                            'window_uid' => $log['window_uid'],
                            "sim_id"     => $simId
                        ));

                        foreach ($log_objs as $log_obj) {
                            if ($log_obj->end_time === '00:00:00') {
                                $log_obj->end_time = gmdate("H:i:s", $log[3]);
                            }
                            $log_obj->mail_task_id = $log[4]['planId'];
                            $log_obj->mail_id = empty($log[4]['mailId']) ? NULL : $log[4]['mailId'];
                            $log_obj->full_coincidence = $result['full'];
                            $log_obj->part1_coincidence = $result['part1'];
                            $log_obj->part2_coincidence = $result['part2'];
                            $log_obj->is_coincidence = $result['has_concidence'];
                            $log_obj->save();
                        }
                        continue;

                    }

                } elseif (self::ACTION_SWITCH == (string)$log[2]) {
                    $log_obj = LogMail::model()->findByAttributes(array(
                        'end_time' => '00:00:00',
                        'sim_id' => $simId
                    ));
                    if (!$log_obj) continue;
                    $log_obj->end_time = gmdate("H:i:s", $log[3]);
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

    public static function getMailInDetail($return)
    {

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

        foreach ($data['data'] as $k => $row) {
            $data['data'][$k]['window'] = self::$subScreens[$data['data'][$k]['window']];
        }
        $headers = array(
            'sim_id' => 'id_симуляции',
            'code' => 'Код входящего письма',
            'window' => 'Тип просмотра',
            'start_time' => 'Игровое время - start',
            'end_time' => 'Игровое время - end'
        );
        if (self::RETURN_DATA == $return) {
            $data['headers'] = $headers;
            $data['title'] = "Логирование работы с Входящими сообщениями - детально";
            return $data;
        } elseif (self::RETURN_CSV == $return) {

            $csv = new ECSVExport($data['data'], true, true, ';');
            $csv->setHeaders($headers);
            $content = $csv->toCSVutf8BOM();
            $filename = 'mail_inbox_detailed.csv';
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = ' . $return . ' метода ' . __CLASS__ . '::' . __METHOD__);
        }
        return true;
    }

    public static function getMailInAggregate($return)
    {

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
        foreach (MailTask::model()->findAll() as $line) {
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
            'sim_id' => 'id_симуляции',
            'code' => 'Код входящего письма',
            'name' => 'Папка мейл-клиента',
            'type_of_importance' => 'Тип письма',
            'readed' => 'Письмо прочтено (да/нет)',
            'plan' => 'Письмо запланировано (да/нет)',
            'reply' => 'На письмо отправлен ответ',
            'mail_task_is_correct' => 'Задача запланирована правильно?',
        );

        if (self::RETURN_DATA == $return) {
            $data['headers'] = $headers;
            $data['title'] = "Логирование работы с Входящими сообщениями - агрегированно";
            return $data;
        } elseif (self::RETURN_CSV == $return) {

            $csv = new ECSVExport($data['data'], true, true, ';');
            $csv->setHeaders($headers);
            $content = $csv->toCSVutf8BOM();
            $filename = 'mail_inbox_agregated.csv';
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = ' . $return . ' метода ' . __CLASS__ . '::' . __METHOD__);
        }
        return true;

    }

    public static function getMailOutDetail($return)
    {
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
            'sim_id' => 'id_симуляции',
            'mail_id' => 'id_исходящего письма',
            'send' => 'Отправлено',
            'start_time' => 'Игровое время - start',
            'end_time' => 'Игровое время - end'
        );
        if (self::RETURN_DATA == $return) {
            $data['headers'] = $headers;
            $data['title'] = "Логирование работы с Исходящими сообщениями - детально";
            return $data;
        } elseif (self::RETURN_CSV == $return) {

            $csv = new ECSVExport($data['data'], true, true, ';');
            $csv->setHeaders($headers);
            $content = $csv->toCSVutf8BOM();
            $filename = 'mail_outbox_detailed.csv';
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = ' . $return . ' метода ' . __CLASS__ . '::' . __METHOD__);
        }
        return true;
    }

    public static function getMailOutAggregate($return)
    {
        $data['data'] = Yii::app()
            ->db
            ->createCommand()
            ->select("l.sim_id
                , ifnull(l.mail_id, '-') AS mail_id
                , if(m.group_id = 3, 'Да', 'Нет') AS send
                , ifnull(group_concat(DISTINCT r.receiver_id), '-') AS receivers
                , ifnull(group_concat(DISTINCT c.receiver_id), '-') AS copies
                , s.text as subject
                , ifnull(t.code, '-') AS code
                , l.full_coincidence
                , l.part1_coincidence
                , l.part2_coincidence
                , if(l.is_coincidence = 1, 'Да', 'Нет') AS is_coincidence
                , s.mail_prefix
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
        foreach ($data['data'] as $key => $value){
            $data['data'][$key]['subject'] = self::getFormattedTheme($value['subject'], $value['mail_prefix']);
            unset($data['data'][$key]['mail_prefix']);
        }
        $headers = array(
            'sim_id' => 'id_симуляции',
            'mail_id' => 'id_исходящего письма',
            'send' => 'Отправлено',
            'receivers' => 'Кому',
            'copies' => 'Копия',
            'subject' => 'Тема',
            'code' => 'Код вложения',
            'full_coincidence' => 'Полное совпадение',
            'part1_coincidence' => 'Совпадение 1',
            'part2_coincidence' => 'Совпадение 2',
            'is_coincidence' => 'Есть совпадение?',
        );
        if (self::RETURN_DATA == $return) {
            $data['headers'] = $headers;
            $data['title'] = "Логирование работы с Исходящими сообщениями - агрегированно";
            return $data;
        } elseif (self::RETURN_CSV == $return) {

            $csv = new ECSVExport($data['data'], true, true, ';');
            $csv->setHeaders($headers);
            $content = $csv->toCSVutf8BOM();
            $filename = 'mail_outbox_agregated.csv';
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = ' . $return . ' метода ' . __CLASS__ . '::' . __METHOD__);
        }
        return true;
    }

    public static function setWindowsLog($simId, $logs, $isLastLog = false)
    {
        if (!is_array($logs)) return false;
        foreach ($logs as $log) {
            assert(isset($log['window_uid']));
            if (self::ACTION_OPEN == (string)$log[2] || self::ACTION_ACTIVATED == (string)$log[2]) {
                if (LogWindow::model()->countByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simId))) {
                    throw(new CException('Previous window is still activated'));
                }
                $log_window = new LogWindow();
                $log_window->sim_id = $simId;
                $log_window->window = $log[1]; // this is ID of Window table
                $log_window->start_time = gmdate("H:i:s", $log[3]);
                $log_window->window_uid = (isset($log['window_uid'])) ? $log['window_uid'] : NULL;
                $log_window->save();
                continue;

            } elseif (self::ACTION_CLOSE == (string)$log[2] || self::ACTION_DEACTIVATED == (string)$log[2]) {
                $windows = LogWindow::model()->findAllByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simId));
                if (0 == count($windows) && false === $isLastLog) {
                    throw(new CException('No active windows. Achtung!' . $simId));
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

                throw new CException("Ошибка"); //TODO:Описание доделать
            }
        }

        return true;
    }

    public static function setDialogs($simId, $logs)
    {
        if (!is_array($logs)) return false;

        foreach ($logs as $log) {
            if (empty($log[4]['dialogId'])) continue;

            $lastDialogIdInMySQL = isset($log[4]['lastDialogId']) ? $log[4]['lastDialogId'] : null;
            $last_dialog = Replica::model()->findByAttributes(['id' => $lastDialogIdInMySQL, 'is_final_replica' => 1]);
            $lastDialogIdAccordingExcel = (null === $last_dialog) ? null : $last_dialog->excel_id;

            if (self::ACTION_OPEN == (string)$log[2] || self::ACTION_ACTIVATED == (string)$log[2]) {


                $last_dialog = new LogDialog();
                $last_dialog->sim_id = $simId;
                $last_dialog->dialog_id = $log[4]['dialogId'];
                $last_dialog->last_id = $lastDialogIdAccordingExcel;
                $last_dialog->start_time = gmdate("H:i:s", $log[3]);
                $last_dialog->save();
                continue;

            } elseif (self::ACTION_CLOSE == (string)$log[2] || self::ACTION_DEACTIVATED == (string)$log[2]) {
                $dialogs = LogDialog::model()->findAllByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simId, 'dialog_id' => $log[4]['dialogId']));
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

                throw new CException("Ошибка"); //TODO:Описание доделать
            }
        }

        return true;
    }

    public static function getDialogs($return, $simulation = null)
    {

        $command = Yii::app()->db->createCommand()
            ->select("l.sim_id,
                    d.code as code,
                    s.title as category,
                    if(d.type_of_init != 'flex', 'System_dial', 'Manual_dial') as type_of_init,
                    l.last_id,
                    l.start_time,
                    l.end_time")
            ->from('log_dialogs l')
            ->leftJoin('replica d', 'l.dialog_id = d.id')
            ->leftJoin('dialog_subtypes s', 'd.dialog_subtype = s.id')
            ->order("l.id");
        if ($simulation !== null) {
            $command->where('l.sim_id=:sim_id', ['sim_id' => $simulation->primaryKey]);
        }
        $data['data'] = $command->queryAll();

        $data['headers'] = array(
            'sim_id' => 'id_симуляции',
            'code' => 'Код события',
            'category' => 'Категория события',
            'type_of_init' => 'Категория события',
            'last_id' => 'Результирующее id_записи',
            'start_time' => 'Игровое время - start',
            'end_time' => 'Игровое время - end'
        );

        if (self::RETURN_DATA == $return) {
            $data['title'] = "Логирование работы с Документами";
            return $data;
        } elseif (self::RETURN_CSV == $return) {
            $csv = new ECSVExport($data['data'], true, true, ';');
            $csv->setHeaders($data['headers']);
            $content = $csv->toCSVutf8BOM();
            $filename = 'dialogs_log.csv';
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = ' . $return . ' метода ' . __CLASS__ . '::' . __METHOD__);
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
            $data['data'][$key]['formula'] = ' ' . $line['formula'];
            $data['data'][$key]['value'] = $line['value'];
        }

        $data['headers'] = array(
            'sim_id' => 'ID симуляции',
            'formula_id' => 'ID формулы',
            'value' => 'Правильный результат?',
            'formula' => 'Формула'
        );

        if (self::RETURN_DATA == $return) {
            $data['title'] = "Логирование Leg_actions - detail";
            return $data;
        } elseif (self::RETURN_CSV == $return) {
            $csv = new ECSVExport($data['data'], true, true, ';');
            $csv->setHeaders($data['headers']);
            $content = $csv->toCSVutf8BOM();
            $filename = 'excel_assesment_detailed_log.csv';
            Yii::app()->getRequest()->sendFile($filename, $content, "text/csv;charset=utf-8", false);
        } else {
            throw new Exception('Не верный параметр $return = ' . $return . ' метода ' . __CLASS__ . '::' . __METHOD__);
        }

        return true;
    }

    /**
     * Медот сделан чтоб упростить почтение (понимание что он делает) условного оператора в combineLogActivityAgregated
     * теперь название говорит само за себя и комментарий там не нужен
     *
     * @param LogActivityAction $activityAction
     * @param $durationByMailCode
     * @param $limit
     * @internal param string $previouseGroupId
     * @return bool
     */
    public static function isCanBeEasyConcatenated($activityAction, $durationByMailCode, $limit) {
        $action = $activityAction->activityAction->getAction();
        if (! $action instanceof MailTemplate) {
            return false;
        }
        $code = $action->getCode();

        // email read more than $limit seconds on total, can break any other activity during aggregation
        if (NULL !== $code && $durationByMailCode[$code] < $limit) {
            return false;
        }

        return (NULL !== $activityAction['group_id']);
    }

    /**
     * @param Simulation $simulation
     *
     * Documentation: Создание агрегированного лога для activity
     * @link: https://maprofi.atlassian.net/wiki/pages/viewpage.action?pageId=9797774
     */
    public static function combineLogActivityAgregated($simulation, $data = null)
    {
        $aggregatedActivity = NULL;

        if (null === $data) {
            $data = $simulation->log_activity_actions;
        }

        // see @link: https://maprofi.atlassian.net/wiki/pages/viewpage.action?pageId=9797774
        // Особенности логики, пункт 1 {
        // Collect main windows subtypes
        $mainScreenWindow = Window::model()->findByAttributes([
            'type'    => 'main screen',
            'subtype' => 'main screen'
        ]);

        $mainMailWindow = Window::model()->findByAttributes([
            'type'    => 'mail',
            'subtype' => 'mail main'
        ]);

        $mainPhoneWindow = Window::model()->findByAttributes([
            'type'    => 'phone',
            'subtype' => 'phone main'
        ]);

        $mainDocumentWindow = Window::model()->findByAttributes([
            'type'    => 'documents',
            'subtype' => 'documents main'
        ]);

        $mainWindowLegActions = [$mainScreenWindow->subtype, $mainMailWindow->subtype, $mainPhoneWindow->subtype, $mainDocumentWindow->subtype];
        // Особенности логики, пункт 1 }

        // collect time by window id {
        $durationByWindowUid = [];
        $durationByMailCode = [];
        foreach ($data as $activityAction) {
            assert($activityAction instanceof LogActivityAction);
            $w_id = $activityAction->window_uid;
            $diff_time = (new DateTime($activityAction->start_time))->diff(new DateTime($activityAction->end_time))->format('%H:%I:%S');
            $durationByWindowUid[$w_id] = (isset($durationByWindowUid[$w_id]))
                ? $durationByWindowUid[$w_id] + TimeTools::TimeToSeconds($diff_time)
                : TimeTools::TimeToSeconds($diff_time);

            if ($activityAction->activityAction->getAction() instanceof MailTemplate) {
                $m_code = $activityAction->activityAction->getAction()->getCode();
                $durationByMailCode[$m_code]= (isset($durationByMailCode[$m_code]))
                    ? $durationByMailCode[$m_code] + TimeTools::TimeToSeconds($diff_time)
                    : TimeTools::TimeToSeconds($diff_time);
            }
        }
        // collect time by window id }

        $limit = Yii::app()->params['public']['skiliksSpeedFactor'] * 10; // 10 real seconds

        foreach ($data as $activityAction) {
            $diff_time = (new DateTime($activityAction->start_time))->diff(new DateTime($activityAction->end_time))->format('%H:%I:%S');
            $legAction = $activityAction->activityAction->getAction();
            if (NULL === $aggregatedActivity) {
                // init new aggregatedActivity at first iteration
                $diff_time = (new DateTime($activityAction->start_time))->diff(new DateTime($activityAction->end_time))->format('%H:%I:%S');
                $aggregatedActivity = new LogActivityActionAgregated();

                $aggregatedActivity->sim_id =                $simulation->id;
                $aggregatedActivity->leg_type =              $activityAction->activityAction->leg_type;
                $aggregatedActivity->leg_action =            $legAction->getCode();
                $aggregatedActivity->activityAction =        $activityAction->activityAction;
                $aggregatedActivity->activity_action_id =    $activityAction->activity_action_id;
                $aggregatedActivity->category =              $activityAction->activityAction->activity->category_id;
                $aggregatedActivity->is_keep_last_category = $activityAction->activityAction->is_keep_last_category;
                $aggregatedActivity->start_time =            $activityAction->start_time;
                $aggregatedActivity->end_time =              $activityAction->end_time;
                $aggregatedActivity->duration =              $diff_time;
            } else {

                // see @link: https://maprofi.atlassian.net/wiki/pages/viewpage.action?pageId=9797774
                // Особенности логики, пункт 1 {
                if ($activityAction->activityAction->getAction() instanceof MailTemplate) {
                    $mail_code = $activityAction->activityAction->getAction()->getCode();
                } else {
                    $mail_code = null;
                }
                $id = $activityAction->window_uid;

                if (NULL === $mail_code) {
                    if (($activityAction->activityAction->getAction() instanceof Window && in_array($activityAction->activityAction->getAction()->getCode(), $mainWindowLegActions)) ||
                        self::isCanBeEasyConcatenated($activityAction, $durationByMailCode, $limit)) {
                        $actionDurationInGameSeconds = TimeTools::TimeToSeconds($diff_time);
                    } else {
                        $actionDurationInGameSeconds = $durationByWindowUid[$id];
                    }
                } else {
                    if ($activityAction->activityAction->getAction() instanceof MailTemplate && MailBox::FOLDER_OUTBOX_ID == $activityAction->activityAction->getAction()->group_id) {
                        $actionDurationInGameSeconds = $durationByWindowUid[$id];
                    } else {
                        $actionDurationInGameSeconds = $durationByMailCode[$mail_code];
                    }
                }

                // Особенности логики, пункт 1 }

                if ($aggregatedActivity->leg_action == ($legAction ? $legAction->getCode() : null) ||
                    $actionDurationInGameSeconds < $limit )
                {
                    // prolong previous activity :
                    $aggregatedActivity->end_time = $activityAction->end_time;
                    $aggregatedActivity->updateDuration();
                } else {
                    // activity and, save it
                    $aggregatedActivity->save();

                    // init new aggregatedActivity for new activity
                    $aggregatedActivity = new LogActivityActionAgregated();

                    $aggregatedActivity->sim_id =                $simulation->id;
                    $aggregatedActivity->leg_type =              $activityAction->activityAction->leg_type;
                    if ($legAction) {
                        $aggregatedActivity->leg_action =            $legAction->getCode();
                    }
                    $aggregatedActivity->activityAction =        $activityAction->activityAction;
                    $aggregatedActivity->activity_action_id =    $activityAction->activity_action_id;
                    $aggregatedActivity->category =              $activityAction->activityAction->activity->category_id;
                    $aggregatedActivity->is_keep_last_category = $activityAction->activityAction->is_keep_last_category;
                    $aggregatedActivity->start_time =            $activityAction->start_time;
                    $aggregatedActivity->end_time =              $activityAction->end_time;
                    $aggregatedActivity->duration =              $diff_time;
                }
            }
        }

        if (NULL !== $aggregatedActivity) {
            $aggregatedActivity->save();
        }
    }

    /**
     * @return string
     */
    private static function getFormattedTheme($text, $prefix)
    {
        return str_replace(['re', 'fwd'], ['Re: ', 'Fwd: '], $prefix) . '' . $text;
    }

    /**
     * @param $simulation
     * @param $logs
     * @return bool
     * @throws CException
     */
    public static function setUniversalLog($simulation, $logs)    {

        if (!is_array($logs)) return false;
        foreach ($logs as $log) {

            if (self::ACTION_OPEN == (string)$log[2] || self::ACTION_ACTIVATED == (string)$log[2]) {
                if (UniversalLog::model()->countByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simulation->id))) {
                    throw(new CException('Previous window is still activated'));
                }
                $universal_log = new UniversalLog();
                $universal_log->sim_id = $simulation->id;
                $universal_log->window_id = $log[1];
                $universal_log->mail_id = empty($log[4]['mailId']) ? NULL : $log[4]['mailId'];
                $universal_log->file_id = empty($log[4]['fileId']) ? null : $log[4]['fileId'];
                $universal_log->dialog_id = empty($log[4]['dialogId']) ? null : $log[4]['dialogId'];
                $universal_log->start_time = date("H:i:s", $log[3]);
                $universal_log->save();
                continue;

            } elseif (self::ACTION_CLOSE == (string)$log[2] || self::ACTION_DEACTIVATED == (string)$log[2]) {
                $universal_logs = UniversalLog::model()->findAllByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simulation->id));
                if (0 === count($universal_logs)) {
                    throw(new CException('No active windows. Achtung!' . $simulation->id));
                }
                if (1 < count($universal_logs)) {
                    throw(new CException('Two or more active windows at one time. Achtung!'));
                }
                foreach ($universal_logs as $universal_log) {
                    if (!empty($log['lastDialogId'])) {
                        $dialog = Replica::model()->findByAttributes(['id' => $log['lastDialogId'], 'is_final_replica' => 1]);
                    }
                    $universal_log->last_dialog_id = (empty($dialog)) ? null : $dialog->excel_id;
                    $universal_log->end_time = date("H:i:s", $log[3]);
                    $universal_log->save();
                }
            } elseif (self::ACTION_SWITCH == (string)$log[2]) {

                continue;

            } else {

                throw new CException("Unknown action: " . $log[2]); //TODO:Описание доделать
            }
        }

        return true;
    }


}