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
            if (!$dialogId) throw new Exception('Не задан диалог', 2);
            
            // получаем uid
            $uid = SessionHelper::getUidBySid($sid);

            // получаем идентификатор симуляции
            $simId = SimulationService::get($uid);
            
            
            // получаем ид текущего диалога, выбираем запись
            $currentDialog = DialogService::get($dialogId);
            
            Logger::debug("curr dialog : {$currentDialog->code} next event :  {$currentDialog->next_event_code} replica number : {$currentDialog->replica_number}");
            
            
            // проверим флаги
            if ($currentDialog->flag != '') {
                // если для данной реплики установлен флаг
                // установим флаг в рамках симуляции
                SimulationService::setFlag($simId, $currentDialog->flag);
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
            
            
            ## new code
            $data = array();
            if ($currentDialog->next_event_code != '' && $currentDialog->next_event_code != '-') {
                // смотрим а не является ли следующее событие у нас диалогом
                if (EventService::isDialog($currentDialog->next_event_code)) {
                    
                    // сразу же отдадим реплики по этому событию - моментально
                    $dialogs = Dialogs::model()->byCodeAndStepNumber($currentDialog->next_event_code, 1)->findAll();
                    foreach($dialogs as $dialog) {
                        $data[] = DialogService::dialogToArray($dialog);
                    }
                }
                else {
                    $result = EventService::processLinkedEntities($currentDialog->next_event_code, $simId);
                    if ($result) {
                        // убьем такое событие чтобы оно не произошло позже
                        EventService::deleteByCode($currentDialog->next_event_code, $simId);
                        return $this->_sendResponse(200, CJSON::encode($result));
                    }    
                }
            }
            else {
                // пробуем загрузить реплики
                if ($currentDialog->is_final_replica != 1) {
                    // если нет, то нам надо продолжить диалог
                    // делаем выборку из диалогов, где code =code,  step_number = (текущий step_number + 1)
                    $dialogs = Dialogs::model()->byCodeAndStepNumber($currentDialog->code, $currentDialog->step_number + 1)->findAll();
                    foreach($dialogs as $dialog) {
                        
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
     
            return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => $data)));
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
