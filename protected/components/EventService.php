<?php



/**
 * Сервис по работе с событиями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventService
{
    /**
     * Добавить событие в симуляцию по коду
     * @param string $code
     * @param Simulation $simulation
     * @param bool $eventTime
     * @return bool
     */
    public static function addByCode($code, $simulation, $eventTime = false)
    {
        if ( ($code == '') || ($code == '-') ) return false;
        if ($code == 'T') return false; // финальная реплика
        
        // проверить есть ли событие по такому коду и если есть то создать его
        $event = EventSample::model()->byCode($code)->find();
        if ($event) {
            // попробуем вытащить delay из диалога
            if ($eventTime) {
                $dialog = DialogService::getFirstReplicaByCode($code);

                if ($dialog) {
                    if ($dialog->delay > 0) { //TODO:Проблемное место
                        $eventTime = GameTime::addMinutesTime($eventTime, $dialog->delay);
                    }
                }
            }
            
            
            if (!$eventTime) $eventTime = $event->trigger_time;
            
            // проверим а есть ли такой триггер
            $eventsTriggers = EventTrigger::model()->bySimIdAndEventId($simulation->id, $event->id)->find();
            if ($eventsTriggers) {
                $eventsTriggers->trigger_time = $eventTime; 
                $eventsTriggers->save();
                return true;
            }
            
            $eventsTriggers = new EventTrigger();
            $eventsTriggers->sim_id         = $simulation->id;
            $eventsTriggers->event_id       = $event->id;
            $eventsTriggers->trigger_time   = $eventTime; 
            $eventsTriggers->save();
        }
    }

    /**
     * @param $code
     * @param Simulation $simulation
     * @return bool
     */
    public static function deleteByCode($code, $simulation)
    {
        $event = EventSample::model()->byCode($code)->find();
        if (!$event) {
            return false;
        } // нет у нас такого события
        
        $eventsTriggers = EventTrigger::model()->bySimIdAndEventId($simulation->id, $event->id)->find();
        if (!$eventsTriggers) {
            return false;
        }
        $eventsTriggers->delete();

        return true;
    }
    
    public static function isDialog($code) {
        return preg_match_all("/E(.*)+/", $code, $matches);
    }

    /**
     * Обработка связанных сущностей типа почты, плана...
     * @param $eventCode
     * @param Simulation $simulation
     * @param bool $fantasticResult
     * @throws Exception
     * @return array
     */
    public static function processLinkedEntities($eventCode, $simulation, $fantasticResult = false)
    {
        // анализ писем
        $code = false;
        $type = false;
        
        if (preg_match_all("/^T$/", $eventCode, $matches)) {
            $code= $eventCode;
            $type = 'T'; // окончательная реплика
        } else if (preg_match_all("/([A-Z]+)(\d+)/", $eventCode, $matches)) {
            $code = $eventCode;
            $type = $matches[1][0]; // Message Yesterday
        }

        if (!$code) return false; // у нас нет связанных сущностей
        
        $result = false;
        if ($type == 'MY') {
            // отдать письмо по коду
            $mailModel = MailBox::model()->byCode($code)->find();
            if ($mailModel) {
                // если входящее письмо УЖЕ пришло (кодировка MY - Message Yesterday)
                //  - то в списке писем должно быть выделено именно это письмо
                return array('result' => 1, 'id' => $mailModel->id, 'eventType' => $type);
            }
        }
        
        if ($type == 'M') {
            // если входящее письмо не пришло (кодировка M) - то указанное письмо должно прийти
            $mailModel = MailBoxService::copyMessageFromTemplateByCode($simulation, $code);
            if (!$mailModel) {
                throw new Exception("cant copy mail by code $code");
            }
            
            if ($mailModel) {
                $mailModel->group_id = 1; //входящие
                $mailModel->save();
                return array('result' => 1, 'id' => $mailModel->id, 'eventType' => $type);
            }
        }
        
        if ($type == 'MSY') {
            // отдать письмо по коду
            $mailModel = MailBox::model()->byCode($code)->find();
            if ($mailModel) {
                // если исходящее письмо уже отправлено  (кодировка MSY - Message Sent Yesterday)
                //  - то в списке писем должно быть выделено именно это письмо
                return array('result' => 1, 'id' => $mailModel->id, 'eventType' => $type);
            }
        }
        
        if ($type == 'MS') {
            // если исходящее письмо не отправлено  (кодировка MS - Message Sent) - то должно открыться окно написания нового письма
            $data = ['result' => 1, 'eventType' => $type];
            if ($fantasticResult) {
                $data['fantastic'] = true;
                /** @var $mailTemplate MailTemplate */
                $mailTemplate = MailTemplate::model()->findByAttributes(['code' => $code]);
                $data['mailFields'] = [
                    'receiver_id' => $mailTemplate->receiver_id,
                    'subjectId' => $mailTemplate->subject_id,
                    'subject' => $mailTemplate->subject_obj->text,
                    'phrases' => [
                        'message' => $mailTemplate->message
                    ]
                ];
            }
            return $data;
        }
        
        if ($type == 'D') {
            // определить документ по коду
            $documentTemplate = DocumentTemplate::model()->byCode($code)->find();
            $templateId = $documentTemplate->id;
            
            $document = MyDocument::model()->byTemplateId($templateId)->bySimulation($simulation->id)->find();

            return array('result' => 1, 'eventType' => $type, 'data' => ['id' => $document->id]);
        }
        
        if ($type == 'P') {
            $task = Task::model()->findByAttributes(['code' => $code]);

            // Example of how not to do:
            // if (!$task) return false;
            // Example of how to do:
            assert($task);
            
            TodoService::add($simulation, $task);
            
            return array('result' => 1, 'eventType' => $type, 'id' => $task->id);
        }
        
        if ($type == 'T') {
            return array('result' => 1, 'eventType' => 1);
        }
        
        return $result;
    }
}


