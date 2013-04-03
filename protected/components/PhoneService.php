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
            return 'fail1';  //todo:Костыль
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
               EventsManager::startEvent($simulation, $dialog->next_event_code, 0, 0, 0);

               $dialog_cancel = $simulation->game_type->getReplica([
                   'code'=>$dialog_code,
                   'replica_number'=>2
               ]);

               $cancel_event = $simulation->game_type->getEventSample([
                   'code'=>$dialog_cancel->next_event_code
               ]);

               $cur_event = EventTrigger::model()->findByAttributes([
                   'sim_id' => $simulation->id,
                   'event_id' => $cancel_event->id
               ]);

               if($cur_event !== null){
                   $cur_event->delete();
               }
           }

           return 'ok';
       } else{
           return 'fail';
       }
    }

    /**
     * Получить список тем для телефона.
     * @param int $id
     * @param Simulation $simulation
     * @return array
     */
    public static function getThemes($id, Simulation $simulation) {

        $character = $simulation->game_type->getCharacter(['code' => $id]);
        $themes = $simulation->game_type->getCommunicationThemes(['character_id' => $character->primaryKey, 'phone' => 1]);
        $list = array();
        foreach($themes as $theme) {
            $list[] = ['themeId' => $theme->id, 'themeTitle' => $theme->text];
        }
        
        return $list;
    }
    
    /**
     * Регистрация исходящих вызовов
     * @param int $simId
     * @param int $characterId 
     */
    public static function registerOutgoing($simId, $characterId, $time, $theme_id = null) {

        $model = new PhoneCall();
        $model->sim_id      = $simId;
        /** @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);
        $model->call_time   = $time;
        $model->call_type   = self::CALL_TYPE_OUTGOING;
        $model->from_id     = $simulation->game_type->getCharacter(['code' => Character::HERO_ID])->primaryKey;
        $model->to_id       = $characterId; // какому персонажу мы звоним
        $model->theme_id    = $theme_id;
        $model->insert();
    }
    
    public static function registerMissed($simId, $dialogId, $time) {
        
        $dialog = Replica::model()->byId($dialogId)->find();
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

        $items = PhoneCall::model()->bySimulation($simulation->id)->findAll();
        $list = array();
        foreach($items as $item) {
            if ($item->call_type == PhoneCall::IN_CALL) {
                $characterId = $item->from_id;
            }            
            if ($item->call_type == PhoneCall::OUT_CALL) {
                $characterId = $item->to_id;
            }
            if ($item->call_type == PhoneCall::MISSED_CALL) {
                $characterId = $item->from_id;
            }

            $list[] = array(
                'name' => (!empty($characters[$characterId]['fio'])) ? $characters[$characterId]['fio'] : $characters[$characterId]['title'],
                'date' => Simulation::formatDateForMissedCalls($item->call_time),
                'type' => $item->call_type,
                'dialog_code' => $item->dialog_code
            );
        }
        
        return $list;
    }
    
    public static function call(Simulation $simulation, $themeId, $characterCode, $time)
    {

            /** @var $communicationTheme CommunicationTheme */
            $character = $simulation->game_type->getCharacter(['code' => $characterCode]);
            $communicationTheme = $simulation->game_type->getCommunicationTheme(['character_id' => $character->primaryKey, 'id' => $themeId, 'phone' => 1]);
            if ($communicationTheme) {
                $eventCode = $communicationTheme->phone_dialog_number;
                if ($eventCode == '' || $eventCode == 'AUTO') {

                    // выдаем автоответчик
                    $data = array();
                    $data[] = self::combineReplicaToHero(array('ch_from' => "$characterCode"));

                    $character = Character::model()->byId($characterCode)->find();

                    if ($character) {
                        $data[0]['title'] = $character->title;
                        $data[0]['name'] = $character->fio;
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
                    if (false == FlagsService::isAllowToStartDialog($simulation, $eventCode)) {
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
                        $data = self::getReplicaByCode($eventCode, $simulation);
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
            }
            $data[] = DialogService::dialogToArray($dialog);
        }

        if (isset($data[0]['ch_from'])) {
            $characterId = $data[0]['ch_from'];
            $character = Character::model()->byId($characterId)->find();
            if ($character) {
                $data[0]['title'] = $character->title;
                $data[0]['name'] = $character->fio;
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
            'step_number'       => '1'
        );
        
        return array_merge($newData, $data);
    }
}


