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
        2 =>  'manual',
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
        33 => 'meeting choice',
        34 => 'meeting gone',
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
                    $log_obj->end_time   = '00:00:00';
                    $log_obj->window_uid = $log['window_uid'];
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
        $simulation = Simulation::model()->findByPk($simId);

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
                    $log_obj->end_time   = '00:00:00';
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
                            'window_uid' => $log['window_uid'],
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

                        if ($result['full'] !== null && $result['full'] !== '-') {
                            $template = $simulation->game_type->getMailTemplate(['code' => $result['full']]);
                            /* @var $parent_action ActivityParent */
                            foreach ($template->termination_parent_actions as $parent_action) {
                                if (!$parent_action->isTerminatedInSimulation($simulation)) {
                                    $parent_action->terminateInSimulation($simulation, gmdate("H:i:s", $log[3]));
                                }
                            };
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

    public static function setMeetingLog($simId, $logs)
    {
        if (!is_array($logs)) {
            return false;
        }

        foreach ($logs as $log) {
            if (!isset($log[4]['meetingId'])) {
                continue;
            }

            if (self::ACTION_OPEN == (string)$log[2] OR self::ACTION_ACTIVATED == (string)$log[2]) {
                $log_obj = LogMeeting::model()->findByAttributes([
                    'sim_id' => $simId,
                    'meeting_id' => $log[4]['meetingId']
                ]);

                if (null === $log_obj) {
                    $log_obj = new LogMeeting();
                }

                $log_obj->sim_id = $simId;
                $log_obj->meeting_id = $log[4]['meetingId'];
                $log_obj->start_time = gmdate("H:i:s", $log[3]);
                $log_obj->end_time   = '00:00:00';
                $log_obj->window_uid = $log['window_uid'];
                $log_obj->save();
            } elseif (self::ACTION_CLOSE == (string)$log[2] || self::ACTION_DEACTIVATED == (string)$log[2]) {
                /** @var LogMeeting $log_obj */
                $log_obj = LogMeeting::model()->findByAttributes(array(
                    'meeting_id'  => $log[4]['meetingId'],
                    'end_time' => '00:00:00',
                    'sim_id'   => $simId
                ));
                if (!$log_obj) {
                    continue;
                }

                $log_obj->end_time = gmdate("H:i:s", $log[3]);
                $log_obj->save();
            }
        }

        return true;
    }

    public static function setWindowsLog($simId, $logs)
    {
        if (!is_array($logs)) return false;
        foreach ($logs as $key => $log) {
            assert(isset($log['window_uid']));
            if (self::ACTION_OPEN == (string)$log[2] || self::ACTION_ACTIVATED == (string)$log[2]) {
                $window = LogWindow::model()->findByAttributes([
                    'end_time' => '00:00:00',
                    'sim_id' => $simId
                ]);

                if ($window) {
                    $window->end_time = gmdate("H:i:s", $log[3]);
                    $window->save();
                    Yii::log(sprintf(
                        'Previous window is still activated. Simulation id %d. Window log id %d',
                        $simId, $window->id
                    ), CLogger::LEVEL_WARNING);
                }

                $log_window = new LogWindow();
                $log_window->sim_id = $simId;
                $log_window->window = $log[1]; // this is ID of Window table
                $log_window->start_time = gmdate("H:i:s", $log[3]);
                $log_window->end_time      = '00:00:00';
                $log_window->window_uid = (isset($log['window_uid'])) ? $log['window_uid'] : NULL;
                $log_window->save();
                continue;

            } elseif (self::ACTION_CLOSE == (string)$log[2] || self::ACTION_DEACTIVATED == (string)$log[2]) {
                $window = LogWindow::model()->findByAttributes([
                    'end_time' => '00:00:00',
                    'sim_id' => $simId,
                    'window_uid' => $log['window_uid']
                ]);

                if ($window) {
                    $window->end_time = gmdate("H:i:s", $log[3]);
                    $window->save();
                } else {
                    Yii::log(sprintf(
                        'Can not close window. Simulation id %d. Log: %s',
                        $simId, serialize($log)
                    ), CLogger::LEVEL_WARNING);
                }
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
                $last_dialog->window_uid = $log['window_uid'];
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
     * @param LogActivityAction[] $data
     * @link: https://maprofi.atlassian.net/wiki/pages/viewpage.action?pageId=9797774
     */
    public static function combineLogActivityAgregated($simulation, $data = null)
    {
        $aggregatedActivity = NULL;

        if (null === $data) {
            /** @var $data LogActivityAction[] */
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

        $mailPreviewWindow = Window::model()->findByAttributes([
            'type'    => 'mail',
            'subtype' => 'mail preview'
        ]);

        $mainWindowLegActions = [$mainScreenWindow->subtype, $mainMailWindow->subtype, $mainPhoneWindow->subtype, $mainDocumentWindow->subtype];
        // Особенности логики, пункт 1 }

        // collect time by window id {
        $durationByWindowUid = [];
        $durationByMailCode = [];
        foreach ($data as $activityAction) {
            /* @var $activityAction LogActivityAction */
            assert($activityAction instanceof LogActivityAction);
            $w_id = $activityAction->window_uid;
            $diff_time = (new DateTime($activityAction->start_time))->diff(new DateTime($activityAction->end_time))->format('%H:%I:%S');
            $durationByWindowUid[$w_id] = (isset($durationByWindowUid[$w_id]))
                ? $durationByWindowUid[$w_id] + timeTools::TimeToSeconds($diff_time)
                : TimeTools::timeToSeconds($diff_time);

            if ($activityAction->activityAction->getAction() instanceof MailTemplate) {
                $m_code = $activityAction->activityAction->getAction()->getCode();
                $durationByMailCode[$m_code]= (isset($durationByMailCode[$m_code]))
                    ? $durationByMailCode[$m_code] + TimeTools::timeToSeconds($diff_time)
                    : TimeTools::timeToSeconds($diff_time);
            }
        }
        // collect time by window id }

        $limit = $simulation->getSpeedFactor() * 10; // 10 real seconds

        foreach ($data as $activityAction) {

            // @todo: fix null activity.end_time when sim stop
            if (null == $activityAction->end_time) {
                $activityAction->end_time = $activityAction->start_time;
                $activityAction->save();
                $activityAction->refresh();
            }

            /** @var $activityAction LogActivityAction */
            $diff_time = (new DateTime($activityAction->start_time))->diff(new DateTime($activityAction->end_time))->format('%H:%I:%S');
            $diff_time_second = (new DateTime($activityAction->end_time))->getTimestamp() - (new DateTime($activityAction->start_time))->getTimestamp();
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
                $aggregatedActivity->start_time =            $activityAction->start_time;
                $aggregatedActivity->end_time =              $activityAction->end_time;

                $aggregatedActivity->duration =              $diff_time;
            } else {

                // see @link: https://maprofi.atlassian.net/wiki/pages/viewpage.action?pageId=9797774
                // Особенности логики, пункт 1 {

                // @2865 написать метод $activityAction->isMailLog() для кода:
                // $activityAction->activityAction->getAction() instanceof MailTemplate
                if ($activityAction->activityAction->getAction() instanceof MailTemplate) {
                    $mail_code = $activityAction->activityAction->getAction()->getCode();
                } else {
                    $mail_code = null;
                }
                $id = $activityAction->window_uid;

                if (NULL === $mail_code) {
                    // @2865 написать метод $activityAction->isWindow() для кода:
                    // $activityAction->activityAction->getAction() instanceof Window
                    if ((
                            $activityAction->activityAction->getAction() instanceof Window &&
                            in_array($activityAction->activityAction->getAction()->getCode(), $mainWindowLegActions)
                        ) ||
                            self::isCanBeEasyConcatenated($activityAction, $durationByMailCode, $limit)
                        ) {
                        // 1
                        $actionDurationInGameSeconds = TimeTools::TimeToSeconds($diff_time);
                    } else {
                        // 2
                        $actionDurationInGameSeconds = $durationByWindowUid[$id];
                    }
                } else {
                    if ($activityAction->activityAction->getAction() instanceof MailTemplate
                        && MailBox::FOLDER_OUTBOX_ID == $activityAction->activityAction->getAction()->group_id && $activityAction->window !== $mailPreviewWindow->id) {
                        // 3
                        $actionDurationInGameSeconds = $durationByWindowUid[$id];
                    } else {
                        $actionDurationInGameSeconds = $diff_time_second;
                    }

                }

                //

                // Особенности логики, пункт 1 }

                if ($aggregatedActivity->leg_action == ($legAction ? $legAction->getCode() : null) || $actionDurationInGameSeconds < $limit )
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
                $universal_log->replica_id = empty($log[4]['dialogId']) ? null : $log[4]['dialogId'];
                $universal_log->start_time = date("H:i:s", $log[3]);
                $universal_log->save();
                continue;

            } elseif (self::ACTION_CLOSE == (string)$log[2] || self::ACTION_DEACTIVATED == (string)$log[2]) {
                /* @var  $universal_logs []UniversalLog */
                $universal_logs = UniversalLog::model()->findAllByAttributes(array('end_time' => '00:00:00', 'sim_id' => $simulation->id));
                if (0 === count($universal_logs)) {
                    throw(new CException('No active windows. Achtung!' . $simulation->id));
                }
                if (1 < count($universal_logs)) {
                    throw(new CException('Two or more active windows at one time. Achtung!'));
                }
                /* @var  $universal_log UniversalLog */
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

    public static function soundSwitcher(Simulation $simulation, $is_play, $sound_alias) {

        $log = new LogIncomingCallSoundSwitcher();
        $log->sim_id = $simulation->id;
        $log->is_play = $is_play;
        $log->sound_alias = $sound_alias;
        $log->game_time = $simulation->getGameTime();
        if(false === $log->save()){
            throw new LogicException("No valid data");
        }
    }


    public static function getMailBoxAggregated(Simulation $simulation) {

       $mail_templates = MailTemplate::model()->findAll("scenario_id = :scenario_id and type = :type_m or type = :type_my",
            [
                'type_my' => 3,
                'type_m' => 1,
                'scenario_id' => $simulation->scenario_id
            ]
        );

        $mail_box = MailBox::model()->findAll("sim_id = :sim_id and (type = :type_m or type = :type_my)",
            [
                'type_my' => 3,
                'type_m' => 1,
                'sim_id' => $simulation->id
            ]
        );

        $data = [];
        foreach($mail_box as $mail){
            // @var $mail MailBox
                $data[$mail->template_id] = [
                    'code'   => $mail->code,
                    'folder' => $mail->folder->name,
                    'readed' => ((int)$mail->readed === 1)?'Да':'Нет',
                    'plan'   => ((int)$mail->plan === 1)?'Да':'Нет',
                    'reply'  =>((int)$mail->reply === 1)?'Да':'Нет',
                    'mail_box'=>$mail->id,
                    'type_of_importance'=>$mail->template->type_of_impportance
                ];
        }
        unset($mail_box);
        foreach($mail_templates as $template) {

            if(!isset($data[$template->id])) {
                $data[$template->id] = [
                    'code' => $template->code,
                    'folder' => 'не пришло',
                    'readed' => 'Нет',
                    'plan'   => 'Нет',
                    'reply'  => 'Нет',
                    'mail_box' => 0,
                    'type_of_importance'=>$template->type_of_impportance
                ];
            }
        }
        unset($mail_templates);
        // add is right mail_task planned  {
        $plan = Window::model()->findByAttributes(['subtype'=>'mail plan']);
        $logMail = array();
        $logs = LogMail::model()->findAllByAttributes(['sim_id'=>$simulation->id,'window'=>$plan->id]);
        foreach ($logs as $log) {
            $logMail[$log->mail_id] = $log;
        }

        $mailTask = array();
        foreach (MailTask::model()->findAll() as $line) {
            $mailTask[$line->id] = $line;
        }

        foreach ($data as $key => $value) {
            $data[$key]['mail_task_is_correct'] = '-';
            $data[$key]['task_id'] = '-';
            if ('Да' === $value['plan'] && 'plan' !== $value['type_of_importance']) {
                $data[$key]['mail_task_is_correct'] = 'W';
            }

            if (isset($logMail[$value['mail_box']])) {
                $mailTaskId = $logMail[$value['mail_box']]->mail_task_id;
                if (null !== $mailTaskId) {
                    $data[$key]['task_id'] = $mailTaskId;
                    $data[$key]['mail_task_is_correct'] = $mailTask[$mailTaskId]->wr;
                }
            }
        }
        return $data;
    }

}