<?php
/**
 * Description of DialogService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogService
{    
    public function getDialog($simId, $dialogId, $time) {
     assert($time !== NULL);
     assert($time !== false);

        /** @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);
        FlagsService::checkFlagsDelay($simulation);
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

        $gameTime = $simulation->getGameTime();
        $currentReplica = DialogService::get($dialogId); // получаем ид текущего диалога, выбираем запись

        EventService::deleteByCode($currentReplica->code, $simulation);
        // set flag 1, @1229
        $this->setFlagByReplica($simulation, $currentReplica);
        // проверим а можно ли выполнять это событие (тип события - диалог), проверим событие на флаги

        if (false == FlagsService::isAllowToStartDialog($simulation, $currentReplica->code)) {
            // событие не проходит по флагам -  не пускаем его
            return [
                'result' => 1,
                'data'   => [],
                'events' => []
            ];
        }

        $phone = new PhoneService();
        $phone->setHistory(
                $simId,
                $time,
                $currentReplica->to_character,
                $simulation->game_type->getCharacter(['code' => Character::HERO_ID]),
                $currentReplica->dialog_subtype,
                $currentReplica->step_number,
                $currentReplica->replica_number,
                $currentReplica->code
        );
        ############################################################

        // Calculate character behavior points
        $replicaPoints = $simulation->game_type->getReplicaPoints(['dialog_id' => $dialogId]);
        /** @var ReplicaPoint[] $replicaPoints */
        try {
            foreach ($replicaPoints as $point) {
                LogHelper::setDialogPoint($dialogId, $simId, $point);
            }
        } catch (Exception $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
        }

        // Тут логируем только выбранную реплику героя
        if (+$currentReplica->replica_number !== 0) {
            LogHelper::setReplicaLog($currentReplica, $simulation);
        }

        $result = [
            'result' => 1,
            'events' => []
        ];

        ## new code
        $data = [];

        if ($currentReplica->next_event_code != '' && $currentReplica->next_event_code != '-') {
            list($dialog, $data, $result) = $this->processNextReplica($currentReplica, $data, $simulation, $result, $gameTime);
        }
        else {
            // пробуем загрузить реплики
            if ($currentReplica->is_final_replica != 1) {
                // если нет, то нам надо продолжить диалог
                // делаем выборку из диалогов, где code =code,  step_number = (текущий step_number + 1)
                $dialogs = $simulation->game_type->getReplicas(['code' => $currentReplica->code, 'step_number' => $currentReplica->step_number + 1]);
                foreach($dialogs as $dialog) {
                    $data[$dialog->excel_id] = DialogService::dialogToArray($dialog);
                }
            }
        }

        ###################
        // теперь подчистим список
        $resultList = $this->clearReplicasByFlags($dialogId, $data, $simulation);
        // Если есть видимые реплики, зависящие от флагов, то все не зависящие удаляем (кроме нулевой)
        /*if (isset($ruleDependentExists)) {
            $resultList = array_diff_key($resultList, $defaultDialogs);
        }*/

        $data = $this->runEventsFromReplicas($resultList, $simulation, $gameTime);

        ###################
        if (isset($data[0]['ch_from'])) {
            $characterId = $data[0]['ch_from'];
            $character = $simulation->game_type->getCharacter(['code' => $characterId]);
            if ($character) {
                $data[0]['title'] = $character->title;
                $data[0]['name'] = $character->fio;
            }
        }

        if (isset($data[0]['ch_to'])) {
            $characterId = $data[0]['ch_to'];
            $character = $simulation->game_type->getCharacter(['code' => $characterId]);
            if ($character) {
                $data[0]['remote_title'] = $character->title;
                $data[0]['remote_name'] = $character->fio;
            }
        }

        if (0 < count($data)) {
            $result['events'][] = [
                'result'    => 1,
                'data'      => $data,
                'eventType' => 1
            ];
        }
//
//        if (false === isset($result['events'])) {
//            $result['events'] = [];
//        }

        return $result;

    }

    /**
     * @param $dialogId
     * @param $data
     * @param $simulation
     * @return array
     */
    public function clearReplicasByFlags($dialogId, $data, $simulation)
    {
        $resultList = $data;
        $defaultDialogs = [];
        $flag = [];
        foreach ($data as $dialogId => $dialog) {
            // @1229
            if (false == FlagsService::isAllowToStartDialog($simulation, $dialog['code'])) {
                // событие не проходит по флагам -  не пускаем его
                unset($resultList[$dialogId]);
                continue;
            }

            $flagInfo = FlagsService::checkRule($dialog['code'], $simulation, $dialog['step_number'], $dialog['replica_number'], $dialogId);

            if ($flagInfo['ruleExists'] === true && $flagInfo['compareResult'] === true && (int)$flagInfo['recId'] == 0) {
                break; // нечего чистиить все выполняется, for current dialog replic
            }

            if ($flagInfo['ruleExists'] === true) { // у нас есть такое правило
                if ($flagInfo['compareResult'] === false && (int)$flagInfo['recId'] > 0) {
                    // правило не выполняется для определнной записи - убьем ее
                    if (isset($resultList[$flagInfo['recId']])) unset($resultList[$flagInfo['recId']]);
                    continue;
                } else {
                    $flag[$dialogId] = $dialog;
                    // правило выполняется но нужно удалить ненужную реплику
                    foreach ($resultList as $key => $val) {
                        if ($key != $flagInfo['recId'] && $val['replica_number'] == $dialog['replica_number']) {
                            unset($resultList[$key]);
                            break;
                        }
                    }
                }

                if ($flagInfo['compareResult'] === false && (int)$flagInfo['recId'] == 0) {
                    //у нас не выполняется все событие полностью
                    $resultList = array();
                    break;
                }
            } elseif ($dialog['replica_number'] != 0) {
                $defaultDialogs[$dialogId] = $dialog;
            }
        }

        foreach ($flag as $flag_replicaId => $flag_replica) {
            foreach ($resultList as $replicaId => $replica) {
                if ($flag_replica['replica_number'] === $replica['replica_number']
                    AND $flag_replica['step_number'] === $replica['step_number']
                        AND $flag_replicaId !== $replicaId
                ) {
                    unset($resultList[$replicaId]);
                    unset($flag[$flag_replicaId]);
                }
            }
        }
        return $resultList;
    }

    /**
     * @param $currentReplica
     * @param $data
     * @param Simulation $simulation
     * @param $result
     * @param $gameTime
     * @return array
     */
    public function processNextReplica(Replica $currentReplica, $data, Simulation $simulation, $result, $gameTime)
    {
        // смотрим а не является ли следующее событие у нас диалогом
        // if next event has delay it can`t statr immediatly
        $dialog = $simulation->game_type->getReplica([
            'code' => $currentReplica->next_event_code,
            'step_number' => 1
        ], ['order' => 'replica_number']);
        $dialog = (is_array($dialog)) ? reset($dialog) : $dialog;

        // isDialog() Wrong!!!
        $isDialog = EventService::isDialog($currentReplica->next_event_code);
        /* @var $currentReplica Replica */
        // @2899
        if( $this->isPlan($currentReplica->next_event_code) && (int)$currentReplica->is_final_replica === 0 ){
            $isDialog = true;
            $step_number = (int)$currentReplica->step_number + 1;
            $dialog = $simulation->game_type->getReplica([
                'code' => $currentReplica->code,
                'step_number' => $step_number
            ], ['order' => 'replica_number']);

            $next_event_code = $currentReplica->code;
            EventsManager::waitEvent($simulation, $currentReplica->next_event_code, date("H:i:s", strtotime('+2 minutes', strtotime($gameTime))));
        } else {
            $next_event_code = $currentReplica->next_event_code;
            $step_number = 1;
        }

        if ( null !== $dialog && ($isDialog || false === $dialog->isEvent()) && empty($dialog->delay) ) {
            // сразу же отдадим реплики по этому событию - моментально
            $dialogs = $simulation->game_type->getReplicas([
                'code' => $next_event_code,
                'step_number' => $step_number]);
            foreach ($dialogs as $dialog) {
                $data[$dialog->excel_id] = DialogService::dialogToArray($dialog);
            }
            return array($dialog, $data, $result);
        } else {
            // запуск следующего события
            $res = EventService::processLinkedEntities($currentReplica->next_event_code, $simulation, $currentReplica->fantastic_result);
            if ($res) {
                // убьем такое событие чтобы оно не произошло позже
                EventService::deleteByCode($currentReplica->next_event_code, $simulation);

                $result['events'][] = $res;
                return array($dialog, $data, $result);
            } else {
                // нет особых правил для этого события - запускаем его
                EventService::addByCode($currentReplica->next_event_code, $simulation, $gameTime);
                return array($dialog, $data, $result);
            }
        }
    }

    /**
     * @param $resultList
     * @param $simulation
     * @param $gameTime
     * @return array
     */
    private function runEventsFromReplicas($resultList, $simulation, $gameTime)
    {
        $data = [];
        // а теперь пройдемся по тем кто выжил и позапускаем события

        foreach ($resultList as $index => $dialog) {
            // Если у нас реплика к герою
            if ($dialog['replica_number'] == 0) {
                LogHelper::setReplicaLog(Replica::model()->findByPk($dialog['id']), $simulation);

                // События типа диалог мы не создаем
                // isDialog() Wrong!!!
                if (!EventService::isDialog($dialog['next_event_code'])) {
                    /** @var $replica Replica */
                    $replica = Replica::model()->findByPk($dialog['id']);
                    $this->setFlagByReplica($simulation, $replica);
                    // создадим событие
                    if ($dialog['next_event_code'] != '' && $dialog['next_event_code'] != '-') {
                        EventService::addByCode($dialog['next_event_code'], $simulation, $gameTime, $replica->fantastic_result);
                    }
                }
            }
            unset($resultList[$index]['replica_number']);
            unset($resultList[$index]['next_event_code']);
            $data[] = $resultList[$index];
        }
        return $data;
    }

    public function parsePlanCode($code) {
        preg_match_all("/P(\d+)/", $code, $matches);
        if (!isset($matches[1])) return false;
        if (!isset($matches[1][0])) return false;
        
        return $matches[1][0];
    }

    public function isPlan($code) {

        preg_match_all("/([A-Z]+)(\d+)/", $code, $matches);

        if(isset($matches[1][0]) && $matches[1][0] === 'P') {
            return true;
        }else{
            return false;
        }
    }

    /**
     * Получение модели диалога
     * @param int $dialogId
     * @return Replica
     */
    public static function get($dialogId) {
        $dialog = Replica::model()->findByPk($dialogId);
        if (!$dialog) throw new Exception("Не могу загрузить модель диалога id : $dialogId", 7);
        return $dialog;    
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
            'ch_to'             => $dialog->to_character->code,
            'dialog_subtype'    => $dialog->dialog_subtype,
            'text'              => $dialog->text,
            'sound'             => $dialog->sound,
            'step_number'       => $dialog->step_number,
            'replica_number'    => $dialog->replica_number,
            'next_event_code'   => $dialog->next_event_code,
            'is_final_replica'  => $dialog->is_final_replica,
            'code'              => $dialog->code,
            'excel_id'          => $dialog->excel_id
        );
    }

    public function setFlagByReplica(Simulation $simulation, Replica $replica) {
        if (NULL !== $replica->flag_to_switch && '' !== $replica->flag_to_switch) {
            $flag = Flag::model()->findByAttributes(['code'=>$replica->flag_to_switch]);
            if($flag->delay === '0'){
                FlagsService::setFlag($simulation, $replica->flag_to_switch, 1);
            }else{
                FlagsService::addFlagDelayAfterReplica($simulation, $flag);
            }
        }
        if (NULL !== $replica->flag_to_switch_2 && '' !== $replica->flag_to_switch_2) {
            $flag = Flag::model()->findByAttributes(['code'=>$replica->flag_to_switch_2]);
            if($flag->delay === '0'){
                FlagsService::setFlag($simulation, $replica->flag_to_switch_2, 1);
            }else{
                FlagsService::addFlagDelayAfterReplica($simulation, $flag);
            }
        }

    }
    
}


