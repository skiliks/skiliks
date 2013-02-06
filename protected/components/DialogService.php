<?php



/**
 * Description of DialogService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogService {
    
    public function __construct() {
        
    }
    
    public function getDialog($simId, $dialogId, $time) {
        if ($dialogId == 0) {
            return
                [
                    'result' => 1,
                    'events' => [
                        [
                            'result' => 1,
                            'data' => [],
                            'eventType' => 1
                        ]
                    ]
               ];
        }

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

        $phone = new PhoneService();
        $phone->setHistory(
                $simId,
                $time,
                $currentDialog->to_character,
                Characters::model()->findByAttributes(['code' => Characters::HERO_ID]),
                $currentDialog->dialog_subtype,
                $currentDialog->step_number,
                $currentDialog->replica_number
        );
        ############################################################

        // запускаем ф-цию расчета оценки {
        // @togo;
        CalculationEstimateService::calculate($dialogId, $simId); // к записи, ид которой пришло с фронта
        // конец расчета оценки }

        $result = [
            'result' => 1,
            'events' => []
        ];

        ## new code
        $data = [];

        if ($currentDialog->next_event_code != '' && $currentDialog->next_event_code != '-') {
            // смотрим а не является ли следующее событие у нас диалогом
            // if next event has delay it can`t statr immediatly
            $dialog = Dialogs::model()->byCode($currentDialog->next_event_code)
                ->byStepNumber(1)
                ->find('', array('order' => 'replica_number'));
            $dialog = (is_array($dialog)) ? reset($dialog) : $dialog;

            $isDialog = EventService::isDialog($currentDialog->next_event_code);

            if (null !== $dialog && ($isDialog || false === $dialog->isEvent()) && empty($dialog->delay)) {
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

        $data = [];
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
            unset($resultList[$index]['replica_number']);
            unset($resultList[$index]['next_event_code']);
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

        $result['events'][] = [
            'result' => 1,
            'data' => $data,
            'eventType' => 1
        ];

        return $result;

    }
    
    public function parsePlanCode($code) {
        preg_match_all("/P(\d+)/", $code, $matches);
        if (!isset($matches[1])) return false;
        if (!isset($matches[1][0])) return false;
        
        return $matches[1][0];
    }

    /**
     * Получение модели диалога
     * @param int $dialogId
     * @return Dialogs
     */
    public static function get($dialogId) {
        $dialog = Dialogs::model()->byId($dialogId)->find();
        if (!$dialog) throw new Exception("Не могу загрузить модель диалога id : $dialogId", 7);
        return $dialog;    
    }
    
    /**
     * Загрузить диалог по коду
     * @param string $code
     * @return Dialogs
     */
    public static function getByCode($code) {
        $dialog = Dialogs::model()->byCode($code)->find();
        if (!$dialog) throw new Exception("Не могу загрузить модель диалога code : $code", 701);
        return $dialog;    
    }
    
    public static function getFirstReplicaByCode($code) {
        $dialog = Dialogs::model()->byCode($code)->byStepNumber(1)->byReplicaNumber(0)->find();
        if (!$dialog) return false; //throw new Exception("Не могу загрузить модель диалога code : $code", 701);
        return $dialog;    
    }
    
    /**
     * Проверяет есть ли событие с таким кодом
     * @param type $code 
     */
    public static function existByCode($code) {
        return (bool)self::getByCode($code);
    }
    
    /**
     * Переводит диалог в массив
     * @param Dialogs $dialog
     * @return array
     */
    public static function dialogToArray($dialog) {
        return array(
            'id'                => $dialog->id,
            'ch_from'           => $dialog->from_character->code,
            'ch_from_state'     => $dialog->ch_from_state,
            'ch_to'             => $dialog->to_character->code,
            'ch_to_state'       => $dialog->ch_to_state,
            'dialog_subtype'    => $dialog->dialog_subtype,
            'text'              => $dialog->text,
            'sound'             => $dialog->sound,
            'step_number'       => $dialog->step_number,
            'replica_number'    => $dialog->replica_number,
            'next_event_code'   => $dialog->next_event_code,
            'is_final_replica'   => $dialog->is_final_replica,
            'code'              => $dialog->code
        );
    }
    
    /**
     * @return mixed array
     */
    public static function getDialogsListForAdminka()
    {
        $dialogs = array();
        
        $characters = array();
        foreach (Characters::model()->findAll() as $character) {
            $characters[$character->id] = $character;
        }
        
        $characterStates = array();
        foreach (CharactersStates::model()->findAll() as $characterState) {
            $characterStates[$characterState->id] = $characterState;
        }
        
        $dialogSubtypes = array();
        foreach (DialogSubtypes::model()->findAll() as $dialogSubtype) {
            $dialogSubtypes[$dialogSubtype->id] = $dialogSubtype;
        }
        
        $events = array();
        foreach (EventsSamples::model()->findAll() as $event) {
            $events[$event->id] = $event;
        }
        
        $codes = array();
        foreach (Dialogs::model()->findAll() as $dialog) {
            $codes[] = $dialog->code;
            $dialogs[] = array(
                'id'    => $dialog->id,
                'cell'  => array(
                    $dialog->id, 
                    $dialog->code, 
                    $characters[$dialog->ch_from]->title,
                    $characterStates[$dialog->ch_from_state]->title,
                    $characters[$dialog->ch_to]->title,
                    $characterStates[$dialog->ch_to_state]->title,
                    $dialogSubtypes[$dialog->dialog_subtype]->title,
                    $dialog->text,
                    $dialog->delay,
                    (7 == $dialog->event_result) ? "нет результата" : $dialog->event_result,
                    $dialog->step_number,
                    $dialog->replica_number,
                    (isset($events[$dialog->next_event])) ? $events[$dialog->next_event]->code : '-',
                    0,
                    (1 == $dialog->is_final_replica) ? "да" : "нет",
                )
            );
        }
        
        return $dialogs;
    }
}


