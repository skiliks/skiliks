<?php



/**
 * Контроллер диалога для клиентской части
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogController extends AjaxController{
    
    protected function _parsePlanCode($code) {
        preg_match_all("/P(\d+)/", $code, $matches);
        //Logger::debug('matches : '.var_export($matches, true));
        if (!isset($matches[1])) return false;
        if (!isset($matches[1][0])) return false;
        
        return $matches[1][0];
    }
    
    /**
     * Загрузка заданного диалога
     * @return type 
     */
    public function actionGet() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);  
            if (!$sid) throw new Exception('Не задан sid', 1);
            
            $dialogId = (int)Yii::app()->request->getParam('dialogId', false);  
            Logger::debug('try to get dialog by id : '.$dialogId);
            //if (!$dialogId) throw new Exception('Не задан диалог', 2);
            if ($dialogId == 0) {
                $result = array();
                $result['result'] = 1;
                $result['events'][] = array(
                    'result' => 1,
                    'data' => array(),
                    'eventType' => 1
                );
                return $this->_sendResponse(200, CJSON::encode($result));
            }
            
            
            // получаем uid
            $uid = SessionHelper::getUidBySid($sid);

            // получаем идентификатор симуляции
            $simId = SimulationService::get($uid);
            
            // определим тип симуляции
            $simType = SimulationService::getType($simId);
            
            // получаем ид текущего диалога, выбираем запись
            $currentDialog = DialogService::get($dialogId);
            
            Logger::debug("curr id: {$dialogId} dialogCode : {$currentDialog->code} nextEvent :{$currentDialog->next_event_code} stepNumber: {$currentDialog->step_number} replicaNumber :{$currentDialog->replica_number}");
            
            
            // проверим флаги
            if ($currentDialog->flag != '') {
                // если для данной реплики установлен флаг
                // установим флаг в рамках симуляции
                Logger::debug("set flag : {$currentDialog->flag}");
                SimulationService::setFlag($simId, $currentDialog->flag);
            }
            
            // проверим а можно ли выполнять это событие (тип события - диалог)
            // проверим событие на флаги
            Logger::debug("check flags for dialog  : {$currentDialog->code}");
            $eventRunResult = EventService::allowToRun($currentDialog->code, $simId, $currentDialog->step_number, $currentDialog->replica_number);
            if ($eventRunResult['compareResult'] === false) {
                // событие не проходит по флагам -  не пускаем его
                return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => array())));
            }
            
            
            // проверим а не диалог ли это и не совпадает ли оно по времени с текущим
            /*$canCreateEvent = true;
            $nextEventDialog = Dialogs::model()->byCode($currentDialog->next_event_code)->find();
            if ($nextEventDialog) {
                $nextEvent = EventsSamples::model()->byCode($currentDialog->next_event_code)->find();
                if ($nextEvent) {
                    $curEvent = EventsSamples::model()->byCode($currentDialog->code)->find();
                    if ($curEvent) {
                        // проверим не совпадает ли оно по времени с нашим текущим диалогом
                        if ($curEvent->trigger_time == $nextEvent->trigger_time) {
                            Logger::debug("event {$currentDialog->next_event_code} was denied!");
                            //$canCreateEvent = FALSE;
                        }    
                    }
                }
            }*/
            
            // добавим событие в очередь для выбранного диалога
            /*if ($canCreateEvent) {
                Logger::debug("try to create event by code : {$currentDialog->next_event_code}");
                $gameTime = SimulationService::getGameTime($simId);
                //$gameTime = $gameTime + 1;
                /////////EventService::addByCode($currentDialog->next_event_code, $simId, $gameTime);
            } */   
            
            ##############################
            // проверим а не ссылается ли эта реплика на событие типа PN
            /*$event = EventsSamples::model()->byId($currentDialog->next_event)->find();
            if ($event) {
                $code = $event->code;
                Logger::debug("check code : {$code}");
                $taskId = $this->_parsePlanCode($event->code); 
                Logger::debug("found task : {$taskId}");
                // Если это задача
                if ($taskId) {
                    // Создать задачу
                    $tasks = new Tasks();
                    $tasks->title = 'Срочно проверить презентацию для ГД'; //$currentDialog->text;
                    $tasks->duration = 60;
                    $tasks->type = 1;
                    $tasks->sim_id = $simId;
                    $tasks->insert();
                    
                    // Добавим ее в туду
                    $todo = new Todo();
                    $todo->sim_id = $simId;
                    $todo->task_id = $tasks->id; // $taskId;
                    $todo->insert();
                }
            }*/
            ########################
            
            // проверка а не звонок ли это чтобы залогировать входящий вызов
            if ($currentDialog->dialog_subtype == 1 && $currentDialog->step_number == 1) {
                
                if ($currentDialog->replica_number == 1) $callType = 0; // входящее
                if ($currentDialog->replica_number == 2) $callType = 2; // пропущенные
                
                        
                $phoneCalls = new PhoneCallsModel();
                $phoneCalls->sim_id = $simId;
                $phoneCalls->call_date = time();
                $phoneCalls->call_type = $callType;
                $phoneCalls->from_id = $currentDialog->ch_from;
                $phoneCalls->to_id = 1;
                $phoneCalls->insert();
            }
            ############################################################
            
            
            // запускаем ф-цию расчета оценки -- 
            // 1) к записи, ид которой пришло с фронта
            CalculationEstimateService::calculate($dialogId, $simId);
            
            /*
            // 2) к записи, если таковая существует, которая имеет code = code записи, полученной с фронта,  
            // step_number = (step_number записи, полученной с фронта  + 1), replica_number=0
            $dialogs = Dialogs::model()->findByAttributes(array(
                'code' => $currentDialog->code,
                'step_number' => $currentDialog->step_number + 1,
                'replica_number' => 0
            ));
            foreach($dialogs as $dialog) {
                CalculationEstimateService::calculate($dialog->id, $simId);
            }*/
            // конец расчета оценки
            
             Logger::debug("check next event : {$currentDialog->next_event_code}");
            
            $result = array();
            $result['result'] = 1;
            $result['events'] = array();
             
            ## new code
            $data = array();
            if ($currentDialog->next_event_code != '' && $currentDialog->next_event_code != '-') {
                // смотрим а не является ли следующее событие у нас диалогом
                if (EventService::isDialog($currentDialog->next_event_code)) {
                    
                    // сразу же отдадим реплики по этому событию - моментально
                    $dialogs = Dialogs::model()->byCodeAndStepNumber($currentDialog->next_event_code, 1)->byDemo($simType)->findAll();
                    foreach($dialogs as $dialog) {
                        Logger::debug("draw replica for : {$dialog->excel_id}");
                        
                        $flagsInfo = FlagsService::skipReplica($dialog, $simId);
                        if ($flagsInfo['action'] == 'skip') continue;   // если реплика не проходи по флагам
                        if ($flagsInfo['action'] == 'break') break;     // этот диалог вообще нельзя отображать по флагам
                        
                        
                        
                        $data[] = DialogService::dialogToArray($dialog);
                    }
                }
                else {
                    $res = EventService::processLinkedEntities($currentDialog->next_event_code, $simId);
                    if ($res) {
                        // убьем такое событие чтобы оно не произошло позже
                        EventService::deleteByCode($currentDialog->next_event_code, $simId);
                        $result['events'][] = $res;
                    }    
                    else {
                        // нет особых правил для этого события - запускаем его
                        Logger::debug("no special rules - replica number 0 dialog : {$currentDialog->code} create event {$currentDialog->next_event_code}");
                        EventService::addByCode($currentDialog->next_event_code, $simId, SimulationService::getGameTime($simId));
                    }
                }
            }
            else {
                // пробуем загрузить реплики
                if ($currentDialog->is_final_replica != 1) {
                    // если нет, то нам надо продолжить диалог
                    // делаем выборку из диалогов, где code =code,  step_number = (текущий step_number + 1)
                    $dialogs = Dialogs::model()->byCodeAndStepNumber($currentDialog->code, $currentDialog->step_number + 1)->byDemo($simType)->findAll();
                    foreach($dialogs as $dialog) {
                        
                        $flagsInfo = FlagsService::skipReplica($dialog, $simId);
                        if ($flagsInfo['action'] == 'skip') continue;   // если реплика не проходи по флагам
                        if ($flagsInfo['action'] == 'break') break;     // этот диалог вообще нельзя отображать по флагам

                        /*
                        // попробуем учесть симуляцию
                        Logger::debug("check flags for dialog : {$dialog->code} id: {$dialog->excel_id} step number : {$dialog->step_number} replica number : {$dialog->replica_number}");
                        $flagInfo = FlagsService::checkRule($dialog->code, $simId, $dialog->step_number, $dialog->replica_number);
                        Logger::debug("flag info : ".var_export($flagInfo, true));
                        if (isset($flagInfo['stepNumber']) && isset($flagInfo['replicaNumber'])) {  // если заданы правила для шага и реплики
                            if ($flagInfo['stepNumber'] == $dialog->step_number && $flagInfo['replicaNumber'] == $dialog->replica_number) {
                                if ($flagInfo['compareResult'] === true) { // если выполняются условия правил флагов
                                    if ($flagInfo['recId'] != $dialog->excel_id) {
                                        Logger::debug("skipped replica excelId : {$dialog->excel_id}");
                                        continue; // эта реплика не пойдет в выборку
                                    }    
                                }
                                else {
                                    // условие сравнение не выполняется
                                    if ($flagInfo['recId'] == $dialog->excel_id) {
                                        Logger::debug("skipped replica excelId : {$dialog->excel_id}");
                                        continue; // эта реплика не пойдет в выборку
                                    }    
                                }
                            }
                        }*/
                        
                        if ((int)$dialog->replica_number == 0) {
                            Logger::debug("replica number 0 dialog : {$dialog->code} create event {$dialog->next_event_code}");
                            EventService::addByCode($dialog->next_event_code, $simId, SimulationService::getGameTime($simId));
                        }    
                        
                        $data[] = DialogService::dialogToArray($dialog);
                    }
                }
            }
            
            
            if (isset($data[0]['ch_from'])) {
                $characterId = $data[0]['ch_from'];
                //Logger::debug("get character title : $characterId");
                $character = Characters::model()->byId($characterId)->find();
                //Logger::debug("found character : ".var_export($character));
                if ($character) {
                    $data[0]['title'] = $character->title;
                    $data[0]['name'] = $character->fio;
                }
            }
            
            
            $result['events'][] = array(
                'result' => 1,
                'data' => $data,
                'eventType' => 1
            );
     
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            Logger::debug('exception : '.  $exc->getMessage());
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage(),
                'code' => $exc->getCode()
            )));
        }
    }
}

?>
