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

            if ($dialogId == 0) {
                $result = array();
                $result['result'] = 1;
                $result['events'][] = array('result' => 1, 'data' => array(), 'eventType' => 1);
                return $this->_sendResponse(200, CJSON::encode($result));
            }
            
            
            // получаем uid
            $uid = SessionHelper::getUidBySid($sid);

            // получаем идентификатор симуляции
            $simId = SimulationService::get($uid);
            
            $gameTime = SimulationService::getGameTime($simId);
            
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
                        $data[$dialog->excel_id] = DialogService::dialogToArray($dialog);
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
                        EventService::addByCode($currentDialog->next_event_code, $simId, $gameTime);
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
                        $data[$dialog->excel_id] = DialogService::dialogToArray($dialog);
                        
                        if ((int)$dialog->replica_number == 0) {
                            Logger::debug("replica number 0 dialog : {$dialog->code} create event {$dialog->next_event_code}");
                            EventService::addByCode($dialog->next_event_code, $simId, $gameTime);
                        }    
                    }
                }
            }
            
            
            ###################
            // теперь подчистим список
            $resultList = $data;
            foreach ($data as $dialogId => $dialog) {
                //Logger::debug("code {$dialog['code']}, $simId, step_number {$dialog['step_number']}, replica_number {$dialog['replica_number']}");
                $flagInfo = FlagsService::checkRule($dialog['code'], $simId, $dialog['step_number'], $dialog['replica_number']);
                //Logger::debug("flag info : ".var_export($flagInfo, true));
                if ($flagInfo['ruleExists']) {  // у нас есть такое правило
                    if ($flagInfo['compareResult'] === false && (int)$flagInfo['recId']>0) {
                        // правило не выполняется для определнной записи - убьем ее
                        if (isset($resultList[ $flagInfo['recId'] ])) unset($resultList[ $flagInfo['recId'] ]);
                        continue;
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
                    /*// События типа диалог мы не создаем
                    if (!EventService::isDialog($dialog['next_event_code'])) {
                        // создадим событие
                        EventService::addByCode($dialog['next_event_code'], $simId, $gameTime);
                    }*/
                }
                unset($resultList[$index]['step_number']);
                unset($resultList[$index]['replica_number']);
                unset($resultList[$index]['next_event_code']);
                unset($resultList[$index]['code']);
                $data[] = $resultList[$index];
            }
            
            ###################
            if (isset($data[0]['ch_from'])) {
                $characterId = $data[0]['ch_from'];
                $character = Characters::model()->byId($characterId)->find();
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
