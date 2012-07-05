<?php



/**
 * Контроллер диалога для клиентской части
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogController extends AjaxController{
    
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
                    
                    
                    // получить событие по коду        
                    Logger::debug('try to get event '.$currentDialog->next_event);
                    //$event = EventsSamples::model()->byId($currentDialog->next_event)->find();
                    
                    $event = EventsSamples::model()->findByAttributes(array(
                            'id' => $currentDialog->next_event
                        ));
                    
       //             Logger::debug('loaded event '.var_export($event, true));
                    if ($event) {
                        $dialogs = Dialogs::model()->byCodeAndStepNumber($event->code, 1)->findAll();

                        
         //               Logger::debug('dialogs : '.  var_export($dialogs, true));
                        foreach($dialogs as $dialog) {
                            $data[] = DialogService::dialogToArray($dialog);
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
     
            return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => $data)));
                 
                 
      

            

            ########################################        
            # OLD CODE
            ######################################
            
            // рассчитываем оценку по данному диалогу
            CalculationEstimateService::calculate($dialogId, $simId);
            
            
            
            
            
            if ($dialog->event_result > 0) { // если данный вариант ответа должен сгенерировать событие
                // смотрим что это может быть за событие
                
                // получаем текущее событие в рамках данной симуляции
                $currentEventId = EventService::getCurrent($simId);
                
                // получить информацию о последующем событии
                $eventChoise = EventsChoices::model()->byEventAndResult($currentEventId, $dialog->event_result)->find();
                if ($eventChoise) {
                    // добавить в очередь новое событие
                    EventService::addToQueue($simId, $eventChoise->dstEventId, time() + $eventChoise->delay);
                }
            }
            
            
            // @todo: загрузить диалог по nextBranch
            $dialog = Dialogs::model()->byBranch($dialog->next_branch)->find();
            if (!$dialog) throw new Exception('Не могу загрузить диалог по ветке', 4);
            
            // @todo: загрузить варианта ответов
            $data = array();
            $data[] = DialogService::dialogToArray($dialog);

            // загрузить те, где branch = next_branch
            if ($dialog->ch_to_state == 1) {  // если этот диалог это обращение к нам, то загружаем варианты ответов
                $dialogs = Dialogs::model()->byBranch($dialog->next_branch)->findAll();
                if (!$dialogs) throw new Exception('Не могу загрузить варианты ответов for '.$dialog->next_branch, 5);
                foreach($dialogs as $dialog) {
                    $data[] = DialogService::dialogToArray($dialog);
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
