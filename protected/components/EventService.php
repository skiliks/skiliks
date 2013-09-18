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
    public static function addByCode($code, $simulation, $eventTime = false, $force_run = false)
    {
        if ( ($code == '') || ($code == '-') ) return false;
        if ($code == 'T') return false; // финальная реплика

        // проверяем что по данному диалого залогирована первая реплика
        // -- значит диалог уже произошел {
        $dialog = $simulation->game_type->getReplica([
            'code'           => $code,
            'step_number'    => 1,
            'replica_number' => 0,
        ]);
        if (null !== $dialog) {
            $replicas = LogReplica::model()->findAllByAttributes([
                'sim_id'     => $simulation->id,
                'replica_id' => $dialog->id,
            ]);

            if (0 < count($replicas)) {
                return false;
            }
        }
        // проверяем что по данному диалого залогирована первая реплика }
        
        // проверить есть ли событие по такому коду и если есть то создать его
        $event = $simulation->game_type->getEventSample(['code' => $code]);
        if ($event) {
            // попробуем вытащить delay из диалога
            if ($eventTime) {
                $dialog = $simulation->game_type->getReplica(['code' => $code]);

                if ($dialog) {
                    if ($dialog->delay > 0) { //TODO:Проблемное место
                        $eventTime = GameTime::addMinutesTime($eventTime, $dialog->delay);
                    }
                }
            }
            
            
            if (!$eventTime) $eventTime = $event->trigger_time;
            
            // проверим а есть ли такой триггер
            /** @var $eventsTriggers EventTrigger */
            $eventsTriggers = EventTrigger::model()->bySimIdAndEventId($simulation->id, $event->id)->find();
            if ($eventsTriggers) {
                $eventsTriggers->trigger_time = $eventTime;
                $eventsTriggers->force_run = $force_run;
                $eventsTriggers->save();
                return true;
            }
            
            $eventsTriggers = new EventTrigger();
            $eventsTriggers->sim_id         = $simulation->id;
            $eventsTriggers->event_id       = $event->id;
            $eventsTriggers->trigger_time   = $eventTime;
            $eventsTriggers->force_run = $force_run;
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
        $event = $simulation->game_type->getEventSample(['code' => $code]);

        if (!$event) {
            // да, давайте скроем все наши ошибки от отладки и будем чинить баги вечно
            // todo: разобраться с этим после релиза
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
        } else if ($type == 'M') {
            // если входящее письмо не пришло (кодировка M) - то указанное письмо должно прийти
            /** @var $mailModel MailBox */
            $mailModel = MailBoxService::copyMessageFromTemplateByCode($simulation, $code);
            $sentDate = (new DateTime($mailModel->sent_at));
            if ($sentDate->format('H:i:s') === '00:00:00') {
                $mailModel->sent_at = $sentDate->format('Y-m-d') . ' ' . $simulation->getGameTime();
            }
            $mailModel->group_id = 1; //входящие
            $mailModel->save();
            return array('result' => 1, 'id' => $mailModel->id, 'fantastic' => !!$fantasticResult, 'eventType' => $type);
        } else if ($type == 'MSY') {
            // отдать письмо по коду
            $mailModel = MailBox::model()->byCode($code)->find();
            if ($mailModel) {
                // если исходящее письмо уже отправлено  (кодировка MSY - Message Sent Yesterday)
                //  - то в списке писем должно быть выделено именно это письмо
                return array('result' => 1, 'id' => $mailModel->id, 'eventType' => $type);
            }
        } else if ($type == 'MS') {
            // если исходящее письмо не отправлено  (кодировка MS - Message Sent) - то должно открыться окно написания нового письма
            $data = ['result' => 1, 'eventType' => $type];
            if ($fantasticResult) {
                $data['fantastic'] = true;
                /** @var $mailTemplate MailTemplate */
                $mailTemplate = $simulation->game_type->getMailTemplate(['code' => $code]);
                if ($mailTemplate->attachments) {
                    $fileTemplate = $mailTemplate->attachments[0]->file;
                    $attachmentId = MyDocument::model()->findByAttributes(['template_id' => $fileTemplate->primaryKey, 'sim_id' => $simulation->primaryKey])->primaryKey;
                } else {
                    $attachmentId = null;
                }
                $parentTemplate = $mailTemplate->getParent();
                $data['mailFields'] = [
                    'receiver_id'  => $mailTemplate->receiver_id,
                    'subjectId'    => $mailTemplate->subject_id,
                    'attachmentId' =>  $attachmentId,
                    'subject'      => $mailTemplate->subject_obj->text,
                    'phrases'      => [
                        'message'          => $mailTemplate->message,
                        'previouseMessage' => $parentTemplate ? $parentTemplate->message : ''
                    ]
                ];
            }
            return $data;
        } else if ($type == 'D') {
            // определить документ по коду
            $documentTemplate = DocumentTemplate::model()->findByAttributes([
                'code'        => $code,
                'scenario_id' => $simulation->scenario_id,
            ]);

            $templateId = $documentTemplate->id;
            
            $document = MyDocument::model()->findByAttributes([
                'template_id' => $templateId,
                'sim_id' => $simulation->id,
            ]);

            return array('result' => 1, 'eventType' => $type, 'data' => ['id' => $document->id]);
        } else if ($type == 'P') {
            $task = Task::model()->findByAttributes(['code' => $code]);

            // Example of how not to do:
            // if (!$task) return false;
            // Example of how to do:
            assert($task);

            DayPlanService::addTask($simulation, $task->id, DayPlan::DAY_TODO, null, true);
            
            return array('result' => 1, 'eventType' => $type, 'id' => $task->id);
        } else if ($type == 'T') {
            return array('result' => 1, 'eventType' => 1);
        }
        
        return $result;
    }

    /**
     *
     */
    public static function getEventsQueueForJs(Simulation $simulation, $eventsQueueDepth = 0)
    {
        $result = [];

        if (0 === $eventsQueueDepth) {
            return $result;
        }

        $gameTime = explode(':', $simulation->getGameTime());

        $gameTime = ($gameTime[0] + $eventsQueueDepth).':'.$gameTime[1].':'.$gameTime[2];

        $events = EventTrigger::model()->findAll([
            'condition' => ' 0 < trigger_time AND trigger_time < :gameTime AND sim_id = :simId ',
            'params'    => [
                'gameTime' => $gameTime,
                'simId'    => $simulation->id,
            ],
            'order' => 'trigger_time'
        ]);
        // display flags for developers only ! :) no chanses for cheatting
        if ($simulation->isDevelopMode()) {
            foreach ($events as $event) {

                $title = $event->event_sample->title.', ID #'.$event->id;

                if (empty($event->event_sample->title)) {
                    if ($event->event_sample->isMail()) {
                        $mail = $simulation->game_type->getMailTemplate(['code' => $event->event_sample->code]);
                        if (null === $mail) {
                            $title = 'Такого письма нет в БД!';
                        } else {
                            $title = sprintf(
                                '<strong>from:</strong> %s, <strong>to:</strong> %s, <strong>subject:</strong> %s',
                                $mail->sender->fio,
                                $mail->recipient->fio,
                                $mail->subject_obj->text.', ID #'.$event->id
                            );
                        }
                    }
                }

                $result[] = [
                    'code'   => $event->event_sample->code,
                    'title'  => $title,
                    'time'   => $event->trigger_time,
                    'isMail' => $event->event_sample->isMail()
                ];
            }
        }

        return $result;
    }
}


