<?php

class EventsManager {

    public function __construct() {

    }

    /**
     * @param $simId
     * @param $eventCode
     * @param $clearEvents
     * @param $clearAssessment
     * @param $delay
     * @return array
     * @throws Exception
     */
    public function startEvent($simId, $eventCode, $clearEvents, $clearAssessment, $delay) {

            $event = EventSample::model()->byCode($eventCode)->find();
            if (!$event) throw new Exception('Не могу определить событие по коду : '.  $eventCode);
            
            // если надо очищаем очерель событий для текущей симуляции
            if ($clearEvents) {
                EventTrigger::model()->deleteAll("sim_id={$simId}");
            }
            
            // если надо очищаем оценки  для текущей симуляции
            if ($clearAssessment) {
                SimulationDialogPoint::model()->deleteAll("sim_id={$simId}");
            }

            $gameTime = GameTime::addMinutesTime(SimulationService::getGameTime($simId), $delay);

            $eventsTriggers = EventTrigger::model()->bySimIdAndEventId($simId, $event->id)->find();
            if ($eventsTriggers) {
                $eventsTriggers->trigger_time = $gameTime;
                $eventsTriggers->save(); // обновляем существующее событие в очереди
            }
            else {
                
                // Добавляем событие
                $eventsTriggers = new EventTrigger();
                $eventsTriggers->sim_id = $simId;
                $eventsTriggers->event_id = $event->id;
                $eventsTriggers->trigger_time = $gameTime;
                $eventsTriggers->insert();
            }
            
            return ['result' => 1];
    }

    public function waitEvent($simId, $eventCode, $eventTime) {
        $event = EventSample::model()->byCode($eventCode)->find();
        $eventsTriggers = EventTrigger::model()->bySimIdAndEventId($simId, $event->id)->find();

        if (!$eventsTriggers) {
            $eventsTriggers = new EventTrigger();
            $eventsTriggers->sim_id = $simId;
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
    public function getState($simulation, $logs) {
        $simId = $simulation->id;
        $gameTime = 0;
        try {
            $this->processLogs($simulation, $logs);

            $simType  = $simulation->type; // определим тип симуляции
            $gameTime = $simulation->getGameTime();

            // обработка задач {
            $task = false; //$this->processTasks($simId);
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

                    $res = EventService::processLinkedEntities($event->code, $simId);
                    if ($res) {
                        $result['events'][] = $res;
                    }

                    $index++;
                }
            }

            // У нас одно событие           
            $dialogs = Replica::model()
                ->byCode($eventCode)
                ->byStepNumber(1)
                ->byDemo($simType)
                ->findAll();

            $data = array();
            foreach($dialogs as $dialog) {
                $data[(int)$dialog->excel_id] = DialogService::dialogToArray($dialog);
            }
            
            // теперь подчистим список
            $resultList = $data;
            $defaultDialogs = [];
            foreach ($data as $dialogId => $dialog) {
                $flagInfo = FlagsService::checkRule(
                    $dialog['code'], 
                    $simId,
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
                        $ruleDependentExists = true;
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

            // Если есть видимые реплики, зависящие от флагов, то все не зависящие удаляем (кроме нулевой)
            if (isset($ruleDependentExists)) {
                $resultList = array_diff_key($resultList, $defaultDialogs);
            }

            $data = array();
            // а теперь пройдемся по тем кто выжил и позапускаем события
            foreach($resultList as $index=>$dialog) {
                // Если у нас реплика к герою
                if ($dialog['replica_number'] == 0) {
                    // События типа диалог мы не создаем
                    if (!EventService::isDialog($dialog['next_event_code'])) {
                        // создадим событие
                        EventService::addByCode($dialog['next_event_code'], $simId, $gameTime);
                    }
                }

                unset($resultList[$index]['step_number']);
                unset($resultList[$index]['replica_number']);
                unset($resultList[$index]['next_event_code']);
                $data[] = $resultList[$index];
            }
            
            if (isset($data[0]['ch_from'])) {
                $characterId = $data[0]['ch_from'];
                $character = Characters::model()->findByAttributes(['code' => $characterId]);
                if ($character) {
                    $data[0]['title'] = $character->title;
                    $data[0]['name'] = $character->fio;
                }
            }

            if (isset($data[0]['ch_to'])) {
                $characterId = $data[0]['ch_to'];
                $character = Characters::model()->findByAttributes(['code' => $characterId]);
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
    public function processLogs($simulation, $logs)
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
        $log_manager = new LogManager();
        $log_manager->setUniversalLog($simId, $logs);

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
                        /** @var Dialogs $currentDialog */
                        $currentDialog = Replica::model()->findByPk($data[4]['lastDialogId']);
                        $updatedDialogs[] = $data[4]['lastDialogId'];

                        if (null !== $currentDialog && $currentDialog->isPhoneCall() && $currentDialog->replica_number != 0) {
                            // update Phone call dialog last_id
                            /** @var $callDialog Dialogs */
                            $callDialog = Replica::model()
                                ->byCode($currentDialog->code)
                                ->byStepNumber(1)
                                ->byReplicaNumber(0)
                                ->find();

                            if (null !== $callDialog) {
                                /** @var $logRecord LogDialogs */
                                $logRecord = LogDialogs::model()
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

    public function processTasks($simId) {
        ###  определение событие типа todo
        // получаем игровое время
        $gameTime = GameTime::addMinutesTime(SimulationService::getGameTime($simId), 9*60);
        // выбираем задачи из плана, которые произойдут в ближайшие 5 минут
        $toTime = GameTime::addMinutesTime($gameTime, 5);
        
        $dayPlan = DayPlan::model()->nearest($gameTime, $toTime)->find();
        if (!$dayPlan) return false;
        
        // загружаем таску
        $task = Task::model()->byId($dayPlan->task_id)->find();
        if (!$task) return false;
        
        return [
            'id' => $task->id,
            'text' => $task->title
        ];
    }
    
    public function getList() {
        
        $eventsSamples = EventSample::model()->findAll();
        $data = [];
        foreach($eventsSamples as $event) {
            $data[] = [
                'id' => $event->id,
                'code' => $event->code,
                'title' => $event->title
            ];
        }
        
        return ['result' => 1, 'data' => $data];
    }
    
}
