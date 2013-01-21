<?php

class EventsManager {
    
    protected $uid;

    public function __construct() {

    }

    public function startEvent($simId, $eventCode, $clearEvents, $clearAssessment, $delay) {
        
        try {
            
            $uid = SessionHelper::getUidBySid();
            if (null === $uid) { 
                throw new CHttpException(200,'Не могу определить пользователя', 2);
            }
                        
            $event = EventsSamples::model()->byCode($eventCode)->find();
            if (!$event) throw new Exception('Не могу определить событие по коду : '.  $eventCode);
            
            // если надо очищаем очерель событий для текущей симуляции
            if ($clearEvents) {
                EventsTriggers::model()->deleteAll("sim_id={$simId}");
            }
            
            // если надо очищаем оценки  для текущей симуляции
            if ($clearAssessment) {
                SimulationsDialogsPoints::model()->deleteAll("sim_id={$simId}");
            }

            $gameTime = GameTime::addMinutesTime(SimulationService::getGameTime($simId), $delay);

            $eventsTriggers = EventsTriggers::model()->bySimIdAndEventId($simId, $event->id)->find();
            if ($eventsTriggers) {
                $eventsTriggers->trigger_time = $gameTime;
                $eventsTriggers->save(); // обновляем существующее событие в очереди
            }
            else {
                
                // Добавляем событие
                $eventsTriggers = new EventsTriggers();
                $eventsTriggers->sim_id = $simId;
                $eventsTriggers->event_id = $event->id;
                $eventsTriggers->trigger_time = $gameTime;
                $eventsTriggers->insert();
            }
            
            return ['result' => 1];
            
        } catch (Exception $exc) {
            return [
                'result' => 0, 
                'message' => $exc->getMessage()
            ];
        }
        
    }
    
    public function getState($simulation) {
        $simId = $simulation->id;
        $gameTime = 0;
        try {

            // данные для логирования {
            LogHelper::setLog($simId, Yii::app()->request->getParam('logs', false));
            $originalNotFilteregLogs = Yii::app()->request->getParam('logs', null); // used after standart logging
            // to update phone call dialogs lastDialogId

            $logs = LogHelper::logFilter(Yii::app()->request->getParam('logs', false)); //Фильтр нулевых отрезков всегда перед обработкой логов
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
            if(is_array($originalNotFilteregLogs)) {
                $updatedDialogs = array();
                foreach ($originalNotFilteregLogs as $data) {
                    if (isset($data[4]) && isset($data[4]['lastDialogId'])) {
                        if (false === in_array($data[4]['lastDialogId'], $updatedDialogs)) {
                            $currentDialog = Dialogs::model()->findByPk($data[4]['lastDialogId']);
                            $updatedDialogs[] =  $data[4]['lastDialogId'];

                            if (null!== $currentDialog &&$currentDialog->isPhoneCall() && $currentDialog->replica_number != 0) {
                                // update Phone call dialog last_id
                                $callDialog = Dialogs::model()
                                    ->byCode($currentDialog->code)
                                    ->byStepNumber(1)
                                    ->byReplicaNumber(0)
                                    ->find();

                                if (null !== $callDialog) {
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
            
            $simType  = $simulation->type; // определим тип симуляции
            $gameTime = $simulation->getGameTime();

            // обработка задач {
            $task = $this->processTasks($simId);
            if ($task) {
                return [
                    'result' => 1, 
                    'data' => $task, 
                    'eventType' => 'task', 
                    'serverTime' => $gameTime
                ];
 
            }
            // обработка задач }
            
            $triggers = EventsTriggers::model()->nearest($simId, $gameTime)->findAll(['limit' => 1]); // получить ближайшее событие
            
            if (count($triggers) == 0) { 
                throw new CHttpException(200, 'Нет ближайших событий', 4); // @todo: investigate - "No events" is exception ?
            }
            
            $result = array('result' => 1);

            $eventCode = false;
            if (count($triggers)>0) {  // если у нас много событий
                $index = 0;
                foreach($triggers as $trigger) {
                    
                    $event = EventsSamples::model()->byId($trigger->event_id)->find();
                    if (null === $event) {
                        throw new CHttpException(200, 'Не могу определить конкретное событие for event '.$trigger->event_id, 5);
                    }
                    
                    $trigger->delete(); // Убиваем обработанное событие

                    if ($index == 0) { $eventCode = $event->code; }
                    
                    // проверим событие на флаги
                    if (!EventService::allowToRun($event->code, $simId, 1, 0)) {
                        continue; // событие не проходит по флагам -  не пускаем его
                    }
                    
                    $res = EventService::processLinkedEntities($event->code, $simId);
                    if ($res) {
                        $result['events'][] = $res;
                    }
                    
                    $index++;
                }
            }            
            
            // У нас одно событие           
            $dialogs = Dialogs::model()->byCode($eventCode)->byStepNumber(1)->byDemo($simType)->findAll();
            
            $data = array();
            foreach($dialogs as $dialog) {
                $data[(int)$dialog->excel_id] = DialogService::dialogToArray($dialog);
            }
            
            // теперь подчистим список
            $resultList = $data;
            foreach ($data as $dialogId => $dialog) {
                $flagInfo = FlagsService::checkRule($dialog['code'], $simId, $dialog['step_number'], $dialog['replica_number'], $dialogId);
                
                if ($flagInfo['ruleExists']===true && $flagInfo['compareResult'] === true && (int)$flagInfo['recId']==0) {
                    break; // нечего чистиить все выполняется
                }
                if ($flagInfo['ruleExists']) {  // у нас есть такое правило
                    if ($flagInfo['compareResult'] === false && (int)$flagInfo['recId'] > 0) {
                        if (isset($resultList[ $flagInfo['recId'] ])) {
                            unset($resultList[ $flagInfo['recId'] ]); // правило не выполняется для определнной записи - убьем ее
                        }
                        continue;
                    }
                    else {
                        // правило выполняется но нужно удалить ненужную реплику
                        foreach($resultList as $key=>$val) {
                            if ($key != $flagInfo['recId'] && $val['replica_number'] == $dialog['replica_number']) {
                                unset($resultList[$key]); break;
                            }
                        }
                    }
                    
                    if ($flagInfo['compareResult'] === false && (int)$flagInfo['recId']==0) {
                        //у нас не выполняется все событие полностью
                        $resultList = array();
                        break;
                    }
                }
                
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
                unset($resultList[$index]['code']);
                $data[] = $resultList[$index];
            }
            
            if (isset($data[0]['ch_from'])) {
                $characterId = $data[0]['ch_from'];
                $character = Characters::model()->byId($characterId)->find();
                if ($character) {
                    $data[0]['title'] = $character->title;
                    $data[0]['name'] = $character->fio;
                }
            }

            $result['serverTime'] = $gameTime;
            if (count($resultList) > 0) {
                $result['events'][] = array('result' => 1, 'eventType' => 1, 'data' => $data);
            }
            
            $result['flagsState'] = FlagsService::getFlagsState($simulation);
            
            return $result;
        } catch (CHttpException $exc) {
            return [
                'result'     => 0,
                'message'    => $exc->getMessage(),
                'code'       => $exc->getCode(),
                'serverTime' => $gameTime,
                'flagsState' => FlagsService::getFlagsState($simulation)
            ];
        }

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
        $task = Tasks::model()->byId($dayPlan->task_id)->find();
        if (!$task) return false;
        
        return [
            'id' => $task->id,
            'text' => $task->title
        ];
    }
    
    public function getList() {
        
        $eventsSamples = EventsSamples::model()->findAll();
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
