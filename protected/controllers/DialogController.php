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
    public function actionGet() 
    {
        try {
            $sid = Yii::app()->request->getParam('sid', false);  
            if (!$sid) { 
                throw new Exception('Не задан sid', 1);
            }
            
            $dialogId = (int)Yii::app()->request->getParam('dialogId', 0);  

            if ($dialogId == 0) {
                return $this->sendJSON(
                    array(
                        'result' => 1,
                        'events' => array(
                            array(
                                'result' => 1, 
                                'data' => array(), 
                                'eventType' => 1
                            )
                        )
                   ));
            }
            
            $uid = SessionHelper::getUidBySid(); // получаем uid
            $simId = SessionHelper::getSimIdBySid($uid); // получаем идентификатор симуляции
            $gameTime = SimulationService::getGameTime($simId);
            $simType = SimulationService::getType($simId); // определим тип симуляции
            $currentDialog = DialogService::get($dialogId); // получаем ид текущего диалога, выбираем запись
            
            // проверим флаги
            if ($currentDialog->flag != '') {
                // если для данной реплики установлен флаг - установим флаг в рамках симуляции
                SimulationService::setFlag($simId, $currentDialog->flag);
            }
            
            // проверим а можно ли выполнять это событие (тип события - диалог), проверим событие на флаги
            $eventRunResult = EventService::allowToRun($currentDialog->code, $simId, $currentDialog->step_number, $currentDialog->replica_number);
            if ($eventRunResult['compareResult'] === false) {
                return $this->sendJSON(array('result' => 1, 'data' => array())); // событие не проходит по флагам -  не пускаем его
            }
            
            // проверка а не звонок ли это чтобы залогировать входящий вызов
            if ($currentDialog->dialog_subtype == 1 && $currentDialog->step_number == 1) {                
                if ($currentDialog->replica_number == 1) {
                    $callType = 0; // входящее
                }
                
                if ($currentDialog->replica_number == 2) {
                    $callType = 2; // пропущенные
                }
                        
                $phoneCalls = new PhoneCallsModel();
                $phoneCalls->sim_id = $simId;
                $phoneCalls->call_date = time();
                $phoneCalls->call_type = $callType;
                $phoneCalls->from_id = $currentDialog->ch_from;
                $phoneCalls->to_id = 1;
                $phoneCalls->insert();
            }
            ############################################################
            
            // запускаем ф-цию расчета оценки { 
            CalculationEstimateService::calculate($dialogId, $simId); // к записи, ид которой пришло с фронта
            // конец расчета оценки }
            
            $result = array(
                'result' => 1,
                'events' => array()
            );
             
            ## new code
            $data = array();
            if ($currentDialog->next_event_code != '' && $currentDialog->next_event_code != '-') {
                // смотрим а не является ли следующее событие у нас диалогом
                // if next event has delay it can`t statr immediatly
                $dialog = Dialogs::model()->byCode($currentDialog->next_event_code)
                    ->byStepNumber(1)
                    ->byReplicaNumber(0)
                    ->find();
                $dialog = (is_array($dialog)) ? reset($dialog) : $dialog;
                
                if (EventService::isDialog($currentDialog->next_event_code) && null !== $dialog && 0 == (int)$dialog->delay) {
                    // сразу же отдадим реплики по этому событию - моментально
                    $dialogs = Dialogs::model()->byCodeAndStepNumber($currentDialog->next_event_code, 1)->byDemo($simType)->findAll();
                    foreach($dialogs as $dialog) {
                        $data[$dialog->excel_id] = DialogService::dialogToArray($dialog);
                    }
                }
                else {
                    // запуск следующего события
                    $res = EventService::processLinkedEntities($currentDialog->next_event_code, $simId);
                    if ($res) {
                        // убьем такое событие чтобы оно не произошло позже
                        EventService::deleteByCode($currentDialog->next_event_code, $simId);
                        $result['events'][] = $res;
                    }    
                    else {
                        // нет особых правил для этого события - запускаем его
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
                    }
                }
            }
            
            ###################
            // теперь подчистим список
            $resultList = $data;
            foreach ($data as $dialogId => $dialog) {
                
                $flagInfo = FlagsService::checkRule($dialog['code'], $simId, $dialog['step_number'], $dialog['replica_number'], $dialogId);
                
                if ($flagInfo['ruleExists']===true && $flagInfo['compareResult'] === true && (int)$flagInfo['recId']==0) {
                    break;  // нечего чистиить все выполняется, for current dialog replic
                }                
                
                if ($flagInfo['ruleExists']===true) {  // у нас есть такое правило
                    if ($flagInfo['compareResult'] === false && (int)$flagInfo['recId']>0) {
                        // правило не выполняется для определнной записи - убьем ее
                        if (isset($resultList[ $flagInfo['recId'] ])) unset($resultList[ $flagInfo['recId'] ]);
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
                        if ($dialog['next_event_code'] != '' && $dialog['next_event_code'] != '-')
                            EventService::addByCode($dialog['next_event_code'], $simId, $gameTime);
                    }
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
     
            $this->sendJSON($result);
        } catch (Exception $e) {
            $this->sendJSON(array(
                'result'  => 0,
                'message' => $e->getMessage(),
                'code'    => $e->getCode()
            ));
        }
        return;
    }
}


