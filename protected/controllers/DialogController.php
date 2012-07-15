<?php



/**
 * Контроллер диалога для клиентской части
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogController extends AjaxController{
    
    protected function _parsePlanCode($code) {
        if (preg_match_all("/P(\d+)/", $code, $matches)) return false;
        if (!isset($matches[1])) return false;
        if (!isset($matches[1][0])) return false;
        
        return $matches[1][0];
    }
    
    /**
     * Загрузка заданного диалога
     * @return type 
     */
    public function actionGet() {
        //try {
            $sid = Yii::app()->request->getParam('sid', false);  
            if (!$sid) throw new Exception('Не задан sid', 1);
            
            
            
            $dialogId = (int)Yii::app()->request->getParam('dialogId', false);  
            Logger::debug('try to get dialog : '.  $dialogId);
            if (!$dialogId) throw new Exception('Не задан диалог', 2);
            
            // получаем uid
            $uid = SessionHelper::getUidBySid($sid);

            // получаем идентификатор симуляции
            $simId = SimulationService::get($uid);
            
            
            // получаем ид текущего диалога, выбираем запись
            $currentDialog = DialogService::get($dialogId);
            
            ##############################
            // проверим а не ссылается ли эта реплика на событие типа PN
            $event = EventsSamples::model()->byId($currentDialog->next_event)->find();
            if ($event) {
                $code = $event->code;
                $taskId = $this->_parsePlanCode($event->code); 
                // Если это задача
                if ($taskId) {
                    // Добавим ее в туду
                    $todo = new Todo();
                    $todo->sim_id = $simId;
                    $todo->task_id = $taskId;
                    $todo->insert();
                }
            }
            ########################
            
            Logger::debug('before calculate');
            // запускаем ф-цию расчета оценки -- 
            // 1) к записи, ид которой пришло с фронта
            CalculationEstimateService::calculate($dialogId, $simId);
            Logger::debug('after calculate');
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
            
            
                
            
                
            
            $data = array();
            // смотрим, есть ли у нее next_event, 
            if ($currentDialog->next_event > 0) {
                Logger::debug('event > 0');
                // если да, то смотрим на delay
                if ($currentDialog->delay == 0) {
                    Logger::debug('delay = 0');
                    // если delay==0 то сразу запускаем данное событие
                    // @todo: сделать запуск события
                    
                    // проверяем а не последняя ли это реплика, если последняя, то не загружаем детей
                    if ($currentDialog->is_final_replica != 1) {
                    
                    
                        // получить событие по коду        
                        Logger::debug('try to get event '.$currentDialog->next_event);

                        $event = EventsSamples::model()->findByAttributes(array('id' => $currentDialog->next_event));


                        if ($event) {
                            $dialogs = Dialogs::model()->byCodeAndStepNumber($event->code, 1)->findAll();
                            foreach($dialogs as $dialog) {
                                $data[] = DialogService::dialogToArray($dialog);
                            }
                        }
                    }
                }
                
                if ($currentDialog->delay > 0) {
                    Logger::debug('delay >0 0');
                    // если delay>0 то добавляем событие в events_triggers
                    $trigger = new EventsTriggers();
                    $trigger->sim_id = $simId;
                    $trigger->event_id = $currentDialog->next_event;
                    $trigger->trigger_time = time() + ($currentDialog->delay / 4);
                    $trigger->insert();
                }
            }
            else {
                
                Logger::debug('event = 0');
                
                if ($currentDialog->is_final_replica != 1) {
                
                    // если нет, то нам надо продолжить диалог
                    // делаем выборку из диалогов, где code =code,  step_number = (текущий step_number + 1)
                    $dialogs = Dialogs::model()->byCodeAndStepNumber(
                            $currentDialog->code, $currentDialog->step_number + 1
                    )->findAll();
                    //Logger::debug('dialogs2 : '.  var_export($dialogs, true));
                    foreach($dialogs as $dialog) {
                        $data[] = DialogService::dialogToArray($dialog);
                    }
                }
            }
     
            return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => $data)));


            
        /*} catch (Exception $exc) {
            Logger::debug('exception : '.  $exc->getMessage());
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage(),
                'code' => $exc->getCode()
            )));
        }*/
    }
}

?>
