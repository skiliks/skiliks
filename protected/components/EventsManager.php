<?php

class EventsManager {

    public function __construct() {

    }

    /**
     * @param \Simulation $simulation
     * @param $eventCode
     * @param bool $clearEvents
     * @param bool $clearAssessment
     * @param int $delay
     * @param null $gameTime
     * @throws Exception
     * @return array
     */
    public static function startEvent(Simulation $simulation, $eventCode, $clearEvents=false, $clearAssessment=false, $delay=0, $gameTime = null)
    {
        if ('MS' == substr($eventCode, 0, 2)) {
            LibSendMs::sendMsByCode($simulation, $eventCode, $gameTime, 1, 1, rand(1000,9999) + rand(100,999), 2);
            return ['result' => 2];
        }

        $event = $simulation->game_type->getEventSample(['code' => $eventCode]);
        if (!$event) throw new Exception('Не могу определить событие по коду : '.  $eventCode);

        // если надо очищаем очерель событий для текущей симуляции
        if ($clearEvents) {
            EventTrigger::model()->deleteAll("sim_id={$simulation->id}");
        }

        // если надо очищаем оценки  для текущей симуляции
        if ($clearAssessment) {
            AssessmentPoint::model()->deleteAll("sim_id={$simulation->id}");
        }

        $gameTime = GameTime::addMinutesTime($simulation->getGameTime(), $delay);

        $eventsTriggers = EventTrigger::model()->bySimIdAndEventId($simulation->id, $event->id)->find();
        if ($eventsTriggers) {
            $eventsTriggers->trigger_time = $gameTime;
            $eventsTriggers->save(); // обновляем существующее событие в очереди
        }
        else {

            // Добавляем событие
            $eventsTriggers = new EventTrigger();
            $eventsTriggers->sim_id = $simulation->id;
            $eventsTriggers->event_id = $event->id;
            $eventsTriggers->trigger_time = $gameTime;
            $eventsTriggers->insert();
        }

        return ['result' => 1];
    }

    /**
     * @param Simulation $simulation
     * @param $eventCode
     * @param $eventTime
     * @return array
     */
    public static function waitEvent($simulation, $eventCode, $eventTime)
    {
        $event = $simulation->game_type->getEventSample(['code' => $eventCode]);
        $eventsTriggers = EventTrigger::model()->bySimIdAndEventId($simulation->id, $event->id)->find();

        if (!$eventsTriggers) {
            $eventsTriggers = new EventTrigger();
            $eventsTriggers->sim_id = $simulation->id;
            $eventsTriggers->event_id = $event->id;
            $eventsTriggers->trigger_time = $eventTime ?: $event->trigger_time;
            $eventsTriggers->insert();
        }

        return ['result' => 1];
    }

    /**
     * Этот монстр делает такое:
     *
     * 1. Процессит логи
     * 2. Берет первое событие из EventTriggers
     * 3. Проверяет по флагам, можно ли отдать это событие
     * @param $simulation Simulation
     * @param $logs
     * @return array
     * @throws CHttpException
     */
    public static function getState($simulation, $logs) {
        $simId = $simulation->id;
        $gameTime = 0;
        try {
            $gameTime = $simulation->getGameTime();
            $endTime = Yii::app()->params['simulation'][$simulation->getTypeLabel()]['end'];

            if (GameTime::timeToSeconds($gameTime) > GameTime::timeToSeconds($endTime)) {
                throw new CHttpException(200, 'Время истекло', 4);
            }

            self::processLogs($simulation, $logs);

            // обработка задач {
            $task = false;
            if ($task) {
                return [
                    'result'     => 1,
                    'data'       => $task,
                    'eventType'  => 'task',
                    'serverTime' => $gameTime
                ];
 
            }
            // обработка задач }
            
            // получить ближайшее событие
            /** @var $triggers EventTrigger[] */
            $triggers = EventTrigger::model()->nearest($simId, $gameTime)->findAll(['limit' => 1]);

            foreach ($triggers as $key => $trigger) {
                if(false === FlagsService::isAllowToStartDialog($simulation, $trigger->event_sample->code)) {
                    $trigger->delete();
                    unset($triggers[$key]);
                }
                if(false === FlagsService::isAllowToSendMail($simulation, $trigger->event_sample->code)) {
                    $trigger->delete();
                    unset($triggers[$key]);
                }
            }

            if (count($triggers) == 0) { 
                // @todo: investigate - "No events" is exception ?
                throw new CHttpException(200, 'Нет ближайших событий', 4);
            }
            
            $result = array('result' => 1);

            $eventCode = false;
            $eventTime = '00:00:00';
            if (count($triggers)>0) {  // если у нас много событий
                $index = 0;
                foreach($triggers as $trigger) {

                    $event = EventSample::model()->byId($trigger->event_id)->find();

                    if (null === $event) {
                        throw new CHttpException(
                            200, 
                            'Не могу определить конкретное событие for event '.$trigger->event_id, 
                            5
                        );
                    }

                    $trigger->delete(); // Убиваем обработанное событие

                    if ($index == 0) {
                        $eventCode = $event->code;
                        $eventTime = $trigger->trigger_time;
                    }

                    $res = EventService::processLinkedEntities($event->code, $simulation, $trigger->force_run);
                    if ($res) {
                        $result['events'][] = $res;
                    }

                    $index++;
                }
            }

            // У нас одно событие           
            $dialogs = $simulation->game_type->getReplicas([
                'code' => $eventCode,
                'step_number' => 1
            ]);

            $data = array();
            foreach($dialogs as $dialog) {
                $data[(int)$dialog->excel_id] = DialogService::dialogToArray($dialog);
            }
            
            // теперь подчистим список
            $resultList = $data;
            $defaultDialogs = [];
            $flag = [];
            foreach ($data as $dialogId => $dialog) {
                $flagInfo = FlagsService::checkRule(
                    $dialog['code'],
                    $simulation,
                    $dialog['step_number'],
                    $dialog['replica_number'],
                    $dialog['excel_id']
                );

                if ($flagInfo['ruleExists']===true && $flagInfo['compareResult'] === true && (int)$flagInfo['recId']==0) {
                    break; // нечего чистить все выполняется
                }
                if ($flagInfo['ruleExists']) {  // у нас есть такое правило
                    if ($flagInfo['compareResult'] === false && (int)$flagInfo['recId'] > 0) {
                        if (isset($resultList[ $flagInfo['recId'] ])) {
                            // правило не выполняется для определнной записи - убьем ее
                            unset($resultList[ $flagInfo['recId'] ]);
                        }
                        continue;
                    }
                    else {
                       $flag[$dialogId] = $dialog;
                        //$ruleDependentExists = true;
                    }

                    // Это условие вообще может ли выполниться?
                    if ($flagInfo['compareResult'] === false && (int)$flagInfo['recId']==0) {
                        //у нас не выполняется все событие полностью
                        $resultList = array();
                        break;
                    }
                } elseif ($dialog['replica_number'] != 0) {
                    $defaultDialogs[$dialogId] = $dialog;
                }
            }

            foreach( $flag as $flag_replicaId => $flag_replica ) {
                foreach( $resultList as $replicaId => $replica ){
                    if( $flag_replica['replica_number'] === $replica['replica_number']
                         AND $flag_replica['step_number'] === $replica['step_number']
                            AND $flag_replicaId !== $replicaId ) {
                        unset($resultList[$replicaId]);
                        unset($flag[$flag_replicaId]);
                    }
                }
            }
            // Если есть видимые реплики, зависящие от флагов, то все не зависящие удаляем (кроме нулевой)
            /*if (isset($ruleDependentExists)) {
                $resultList = array_diff_key($resultList, $defaultDialogs);
            }*/

            $data = array();
            // а теперь пройдемся по тем кто выжил и позапускаем события
            foreach($resultList as $index=>$dialog) {
                // Если у нас реплика к герою
                if ($dialog['replica_number'] == 0) {
                    // События типа диалог мы не создаем
                    // isDialog() Wrong!!!
                    if (!EventService::isDialog($dialog['next_event_code'])) {
                        // создадим событие
                        EventService::addByCode($dialog['next_event_code'], $simulation, $gameTime);
                    }
                }

                unset($resultList[$index]['step_number']);
                unset($resultList[$index]['replica_number']);
                unset($resultList[$index]['next_event_code']);
                $data[] = $resultList[$index];
            }
            
            if (isset($data[0]['ch_from'])) {
                $characterId = $data[0]['ch_from'];
                $character = $simulation->game_type->getCharacter(['code' => $characterId]);
                if ($character) {
                    $data[0]['title'] = $character->title;
                    $data[0]['name'] = $character->fio;
                }
            }

            if (isset($data[0]['ch_to'])) {
                $characterId = $data[0]['ch_to'];
                $character = $simulation->game_type->getCharacter(['code' => $characterId]);
                if ($character) {
                    $data[0]['remote_title'] = $character->title;
                    $data[0]['remote_name'] = $character->fio;
                }
            }

            $result['serverTime'] = $gameTime;
            if (count($resultList) > 0) {
                $result['events'][] = array('result' => 1, 'eventType' => 1, 'eventTime' => $eventTime, 'data' => $data);
            }
            
            $result['flagsState'] = FlagsService::getFlagsStateForJs($simulation);
            
            return $result;
        } catch (CHttpException $exc) {
            return [
                'result'     => 0,
                'message'    => $exc->getMessage(),
                'code'       => $exc->getCode(),
                'serverTime' => $gameTime,
                'flagsState' => FlagsService::getFlagsStateForJs($simulation)
            ];
        }

    }

    /**
     * Extracted logging-related code
     *
     * @param $simulation Simulation
     * @param array $logs
     */
    public static function processLogs($simulation, $logs)
    {
        $simId = $simulation->primaryKey;

        // данные для логирования {
        LogHelper::setLog($simId, $logs);

        $originalNotFilteregLogs = $logs; // used after standart logging

        // to update phone call dialogs lastDialogId

        $logs = LogHelper::logFilter($logs); //Фильтр нулевых отрезков всегда перед обработкой логов

        /** @todo: нужно после беты убрать фильтр логов и сделать нормальное открытие mail preview */
        try {
            LogHelper::setWindowsLog($simId, $logs);
        } catch (CException $e) {
            // @todo: handle
        }

        LogHelper::setUniversalLog($simulation, $logs);

        LogHelper::setDocumentsLog($simId, $logs); //Пишем логирование открытия и закрытия документов
        LogHelper::setMailLog($simId, $logs);

        LogHelper::setDialogs($simId, $logs);
        // данные для логирования }

        // update phone call dialogs lastDialogId {
        if (is_array($originalNotFilteregLogs)) {
            $updatedDialogs = array();
            foreach ($originalNotFilteregLogs as $data) {
                if (isset($data[4]) && isset($data[4]['lastDialogId'])) {
                    if (false === in_array($data[4]['lastDialogId'], $updatedDialogs)) {
                        $currentDialog = $simulation->game_type->getReplica(['id' => $data[4]['lastDialogId']]);
                        $updatedDialogs[] = $data[4]['lastDialogId'];

                        if (null !== $currentDialog && $currentDialog->isPhoneCall() && $currentDialog->replica_number != 0) {
                            // update Phone call dialog last_id
                            $callDialog = $simulation->game_type->getReplica([
                                'code' => $currentDialog->code,
                                'step_number' => 1,
                                'replica_number' => 0
                            ]);

                            if (null !== $callDialog) {
                                $logRecord = LogDialog::model()
                                    ->bySimulationId($simId)
                                    ->byDialogId($callDialog->id)
                                    ->orderById('DESC')
                                    ->find();
                                if (null !== $logRecord) {
                                    $logRecord->last_id = $currentDialog->excel_id;
                                    $logRecord->save();
                                }
                            }
                        }
                    }
                }
            }
        }
        // update phone call dialogs lastDialogId }
    }
}
