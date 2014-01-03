<?php


/**
 * Сервис по работе с телефоном
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class PhoneService {
    const CALL_TYPE_OUTGOING = 1;
    /**
     * @param $simId
     * @param $time
     * @param Character $from_character
     * @param Character $to_character
     * @param $dialog_subtype
     * @param $step_number
     * @param $replica_number
     * @return bool
     */
    public function setHistory(
        $simId,
        $time,
        $from_character,
        $to_character,
        $dialog_subtype,
        $step_number,
        $replica_number, $dialog_code) {
        
        // проверка а не звонок ли это чтобы залогировать входящий вызов
            if ( $dialog_subtype == 1 && $step_number == 1 ) {                
                if ( $replica_number == 1 ) {
                    $callType = 0; // входящее
                } else if ( $replica_number == 2 ) {
                    $callType = 2; // пропущенные
                } else {
                    assert(false);
                }
                        
                $phoneCalls = new PhoneCall();
                $phoneCalls->sim_id = $simId;
                $phoneCalls->call_time = $time;
                $phoneCalls->call_type = $callType;
                $phoneCalls->from_id = $from_character->primaryKey;
                $phoneCalls->to_id = $to_character->primaryKey;
                $phoneCalls->dialog_code = $dialog_code;
                $phoneCalls->insert();
                return true;
                
            } else {
                
                return false;
                
            }
        
            
    }

    /**
     * @param $simId
     * @param $dialog_code
     * @return string
     */
    public function callBack(Simulation $simulation, $dialog_code)
    {
        $template = $simulation->game_type->getEventSample(['code'=>'S1.2']); //todo:Костыль

        $ev = EventTrigger::model()->findByAttributes([
            'sim_id'   => $simulation->id,
            'event_id' => $template->id
        ]); //todo:Костыль

        $dialog = $simulation->game_type->getReplica(['code'=>$dialog_code, 'replica_number'=>1]);

        if($ev === null && $dialog->next_event_code == 'E1') {
            return 'fail';  //todo:Костыль
        }

        if (!empty($dialog->next_event_code)) {
            $event = $simulation->game_type->getEventSample([
               'code'=>$dialog->next_event_code
            ]);

            $trigger = EventTrigger::model()->findByAttributes([
               'event_id' => $event->id,
               'sim_id'   => $simulation->id
            ]);

            if ($trigger === null) {
               return 'fail '.$event->id;
            } else {
                try {
                    EventsManager::startEvent($simulation, $dialog->next_event_code);
                } catch (Exception $e) {
                    Yii::log($e->getMessage(), CLogger::LEVEL_WARNING);
                }

                /* Рекурсию лучше не применять */
                $next_code = $this->deleteCancelReplica($simulation, $dialog_code);
                $call = PhoneCall::model()->findByAttributes(['sim_id'=>$simulation->id, 'dialog_code'=>$dialog_code]);
                if(null !== $call) {
                    $next_code = $this->deleteCancelReplica($simulation, $next_code);
                    $call = PhoneCall::model()->findByAttributes(['sim_id'=>$simulation->id, 'dialog_code'=>$next_code]);
                    if(null !== $call) {
                        $this->deleteCancelReplica($simulation, $next_code);
                        PhoneCall::model()->findByAttributes(['sim_id'=>$simulation->id, 'dialog_code'=>$next_code]);
                    }
                }

            }

            return 'ok';
        } else {
            return 'fail';
        }
    }

    /**
     * Получить список тем для телефона.
     * @param int $code
     * @param Simulation $simulation
     * @return array
     */
    public static function getThemes($code, Simulation $simulation) {

        $character = $simulation->game_type->getCharacter(['code' => $code]);
        $themes = $simulation->game_type->getOutgoingPhoneThemes(['character_to_id'=>$character->id]);
        $list = array();
        /*foreach($themes as $theme) {
            if(false === $theme->isBlockedByFlags($simulation) && false === $theme->themeIsUsed($themes_usage)) {
                $list[] = ['themeId' => $theme->id, 'themeTitle' => $theme->text];
            }
        }*/
        foreach($themes as $theme) {
            if(false === $theme->isBlockedByFlags($simulation) && false === $theme->themeIsUsed($simulation)) {
                $list[] = ['themeId' => $theme->theme_id, 'themeTitle' => $theme->theme->text];
            }
        }
        
        return $list;
    }
    
    /**
     * Регистрация исходящих вызовов
     * @param int $simId
     * @param int $characterCode
     */
    public static function registerOutgoing($simId, $characterCode, $time, $themeId = null) {

        $model = new PhoneCall();
        $model->sim_id      = $simId;
        /** @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);
        $model->call_time   = $time;
        $model->call_type   = self::CALL_TYPE_OUTGOING;
        $model->from_id     = $simulation->game_type->getCharacter(['code' => Character::HERO_CODE])->primaryKey;
        $model->to_id       = $simulation->game_type->getCharacter(['code' => $characterCode])->primaryKey; // какому персонажу мы звоним
        $model->theme_id    = $themeId;
        $model->insert();
    }
    
    public static function registerMissed($simId, $dialogId, $time) {
        
        $dialog = Replica::model()->findByPk($dialogId);
        if (!$dialog) throw new Exception("Не могу определить диалог для id {$dialogId}");
        
        $model = new PhoneCall();
        $model->sim_id      = $simId;
        $model->call_time   = date("H:i:s", $time);
        $model->call_type   = 2;
        $model->from_id     = $dialog->ch_to;
        $model->to_id       = 1; // какому персонажу мы звоним
        $model->insert();
    }
    
    /**
     * @param Simulation $simulation
     * @return mixed array
     */
    public static function getMissedCalls($simulation)
    {
        $charactersList = $simulation->game_type->getCharacters([]);
        $characters = array();
        foreach($charactersList as $character) {
            $characters[$character->id] = array(
                'fio'   => $character->fio,
                'title' => $character->title 
            );
        }

        $items = PhoneCall::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $list = array();
        foreach($items as $item) {
            if ($item->call_type == PhoneCall::IN_CALL) {
                $characterId = $item->from_id;
            } else if ($item->call_type == PhoneCall::OUT_CALL) {
                $characterId = $item->to_id;
            } else  if ($item->call_type == PhoneCall::MISSED_CALL) {
                $characterId = $item->from_id;
            } else {
                throw new Exception('Unknown phone call type');
            }

            // skip OUT calls
            if ($item->call_type != PhoneCall::MISSED_CALL) {
                continue;
            }

            $list[] = array(
                'name'         => (!empty($characters[$characterId]['fio'])) ? $characters[$characterId]['fio'] : $characters[$characterId]['title'],
                'date'         => $simulation->formatDateForMissedCalls($item->call_time),
                'type'         => $item->call_type,
                'is_displayed' => (bool)$item->is_displayed,
                'dialog_code'  => $item->dialog_code
            );
        }
        
        return $list;
    }

    /**
     * @param Simulation $simulation
     * @return mixed array
     */
    public static function markMissedCallsDisplayed($simulation)
    {
        $phoneCalls = PhoneCall::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        foreach($phoneCalls as $phoneCall) {
            $phoneCall->is_displayed = 1;
            $phoneCall->save();
        }
    }

    /**
     * @param Simulation $simulation
     * @param integer $themeId (Theme.id)
     * @param varchar $characterCode
     * @param varchar $time
     *
     * @return array
     */
    public static function call(Simulation $simulation, $themeId, $characterCode, $time)
    {
        $character = $simulation->game_type->getCharacter(['code' => $characterCode]);

        $theme = $simulation->game_type->getOutgoingPhoneTheme([
            'character_to_id' => $character->id,
            'theme_id' => $themeId
        ]);
        if ($theme->dialog_code === '' || $theme->dialog_code === 'AUTO') {

                // выдаем автоответчик
                $data = array();
                $data[] = self::combineReplicaToHero(array('ch_from' => "$characterCode"));
                $data[0]['media_file_name'] = 'phone/S1.4.4.1'; // автоответчик-женщина
                $data[0]['media_type'] = 'wav'; // автоответчик-женщина

                $character = $simulation->game_type->getCharacter(['code' => $characterCode]);

                if ($character) {
                    $data[0]['title'] = $character->title;
                    $data[0]['name']  = $character->fio;
                    if ($character->isFemale()) {
                        $data[0]['media_file_name'] = 'phone/S1.4.4.2'; // автоответчик-женщина
                        $data[0]['media_type'] = 'wav'; // автоответчик-женщина
                    }
                }
                PhoneService::registerOutgoing($simulation->id, $characterCode, $time);
                return array(
                    'result' => 1,
                    'events' => array(
                        array(
                            'result' => 1,
                            'data' => $data,
                            'eventType' => 1
                        )
                     )
                );
            } else {

                // у нас есть событие
                // сгенерируем событие

                // проверим а позволяют ли флаги нам запускать реплику
                if (false == FlagsService::isAllowToStartDialog($simulation, $theme->dialog_code)) {
                    // событие не проходит по флагам -  не пускаем его
                    return [
                        'result' => 1,
                        'events' => []
                    ];
                }
                $call = PhoneCall::model()->findByAttributes(['sim_id'=>$simulation->id, 'theme_id'=>$themeId]);
                if($call !== null){

                    return [
                        'result' => 1,
                        'params' => 'already_call',
                        'events' => []
                    ];

                } else {
                    $data = self::getReplicaByCode($theme->dialog_code, $simulation);
                    PhoneService::registerOutgoing($simulation->id, $characterCode, $time, $themeId);
                    return [
                        'result' => 1,
                        'events' => array(
                            array(
                                'result' => 1,
                                'data' => $data,
                                'eventType' => 1
                            )
                        )
                    ];
                }

            }
    }

    public static function getReplicaByCode($eventCode, Simulation $simulation) {
        $dialogs = $simulation->game_type->getReplicas(['code' => $eventCode, 'step_number' => 1]);

        $data = array();
        foreach($dialogs as $dialog) {
            // Если у нас реплика к герою
            if ($dialog->replica_number == 0) {
                // События типа диалог мы не создаем
                // isDialog() Wrong!!!
                if (!EventService::isDialog($dialog->next_event_code)) {
                    // создадим событие
                    EventService::addByCode($dialog->next_event_code, $simulation, $simulation->getGameTime());
                }

                // Log income replica
                LogHelper::setReplicaLog($dialog, $simulation);
            }
            $data[] = DialogService::dialogToArray($dialog);
        }

        if (isset($data[0]['ch_from'])) {
            $heroId = $data[0]['ch_from'];
            $characterId = $data[0]['ch_to'];
            $hero = $simulation->game_type->getCharacter(['code' => $heroId]);
            $character = $simulation->game_type->getCharacter(['code' => $characterId]);
            if (null !== $hero && null !== $character) {
                $data[0]['title'] = $hero->title;
                $data[0]['name'] = $hero->fio;
                $data[0]['remote_name'] = $character->fio;
                $data[0]['remote_title'] = $character->title;
            }
        }

        return $data;
    }
    
    private static function combineReplicaToHero($newData)
    {
        $data = array(
            'id'                => '0',
            'ch_from_state'     => '1',
            'ch_to'             => '1',
            'ch_to_state'       => '1',
            'dialog_subtype'    => '2',
            'text'              => 'Меня нет на месте. Перезвоните мне в следующий раз',
            'sound'             => '#',
            'duration'          => '5',
            'step_number'       => '1',
            'code'              => 'None'
        );
        
        return array_merge($newData, $data);
    }

    public function deleteCancelReplica(Simulation $simulation, $dialog_code) {
        $dialog_cancel = $simulation->game_type->getReplica([
            'code'=>$dialog_code,
            'replica_number'=>2
        ]);
        if(null === $dialog_cancel){
            return '';
        }
        $cancel_event = $simulation->game_type->getEventSample([
            'code'=>$dialog_cancel->next_event_code
        ]);

        if (null === $cancel_event) {
            return '';
        }

        $cur_event = EventTrigger::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'event_id' => $cancel_event->id
        ]);
        if($cur_event !== null){
            $cur_event->delete();
        }
        return $dialog_cancel->next_event_code;
    }
}


