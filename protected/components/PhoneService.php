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
     * @param Characters $from_character
     * @param Characters $to_character
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
                        
                $phoneCalls = new PhoneCallsModel();
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

    public function callBack($simId, $dialog_code) {

       $template = EventSample::model()->findByAttributes(['code'=>'S1.2']);//todo:Костыль
       $ev = EventTrigger::model()->findByAttributes(['sim_id'=>$simId, 'event_id'=>$template->id]);//todo:Костыль

           $dialog = Dialog::model()->findByAttributes(['code'=>$dialog_code, 'replica_number'=>1]);
        if($ev === null and $dialog->next_event_code == 'E1'){ return 'fail'; }//todo:Костыль
           $manager = new EventsManager();
           if(!empty($dialog->next_event_code)) {
               $event = EventSample::model()->findByAttributes(['code'=>$dialog->next_event_code]);
               $trigger = EventTrigger::model()->findByAttributes(['event_id' => $event->id, 'sim_id' => $simId]);//Logger::write($dialog->next_event_code);
               if($trigger !== null){
                   $manager->startEvent($simId, $dialog->next_event_code, 0, 0, 0);
                   $dialog_cancel = Dialog::model()->findByAttributes(['code'=>$dialog_code, 'replica_number'=>2]);
                   $cancel_event = EventSample::model()->findByAttributes(['code'=>$dialog_cancel->next_event_code]);
                   $cur_event = EventTrigger::model()->findByAttributes(['sim_id' => $simId, 'event_id' => $cancel_event->id]);
                   if($cur_event !== null){
                       $cur_event->delete();
                   }
                }else{
                   return 'fail';
               }

           return 'ok';

       } else{
           return 'fail';
       }
    }
    /**
     * Получить список тем для телефона.
     * @param int $id
     * @return array
     */
    public static function getThemes($id) {

        $character = Characters::model()->findByAttributes(['code' => $id]);
        $themes = CommunicationTheme::model()->byCharacter($character->primaryKey)->byPhone()->findAll();
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
    public static function registerOutgoing($simId, $characterId, $time) {

        $model = new PhoneCallsModel();
        $model->sim_id      = $simId;
        $model->call_time   = $time;
        $model->call_type   = self::CALL_TYPE_OUTGOING;
        $model->from_id     = Characters::model()->findByAttributes(['code' => Characters::HERO_ID])->primaryKey;
        $model->to_id       = Characters::model()->findByAttributes(['code' => $characterId])->primaryKey; // какому персонажу мы звоним
        $model->insert();
    }
    
    public static function registerMissed($simId, $dialogId, $time) {
        
        $dialog = Dialog::model()->byId($dialogId)->find();
        if (!$dialog) throw new Exception("Не могу определить диалог для id {$dialogId}");
        
        $model = new PhoneCallsModel();
        $model->sim_id      = $simId;
        $model->call_time   = date("H:i:s", $time);
        $model->call_type   = 2;
        $model->from_id     = $dialog->ch_to;
        $model->to_id       = 1; // какому персонажу мы звоним
        $model->insert();
    }
    
    /**
     * @param Simulations $simulation
     * @return mixed array
     */
    public static function getMissedCalls($simulation)
    {
        $charactersList = Characters::model()->findAll();
        $characters = array();
        foreach($charactersList as $character) {
            $characters[$character->id] = array(
                'fio'   => $character->fio,
                'title' => $character->title 
            );
        }

        $items = PhoneCallsModel::model()->bySimulation($simulation->id)->findAll();
        $list = array();
        foreach($items as $item) {
            if ($item->call_type == PhoneCallsModel::IN_CALL) {
                $characterId = $item->from_id;
            }            
            if ($item->call_type == PhoneCallsModel::OUT_CALL) {                
                $characterId = $item->to_id;
            }
            if ($item->call_type == PhoneCallsModel::MISSED_CALL) {
                $characterId = $item->from_id;
            }

            $list[] = array(
                'name' => (!empty($characters[$characterId]['fio'])) ? $characters[$characterId]['fio'] : $characters[$characterId]['title'],
                'date' => Simulations::formatDateForMissedCalls($item->call_time),
                'type' => $item->call_type,
                'dialog_code' => $item->dialog_code
            );
        }
        
        return $list;
    }
    
    public static function call($simulation, $themeId, $characterId, $time)
    {
        $result = null;
        
        // нам передана тема
        if ($themeId > 0) {
            /** @var $communicationTheme CommunicationTheme */
            $character = Characters::model()->findByAttributes(['code' => $characterId]);
            $communicationTheme = CommunicationTheme::model()->byCharacter($character->primaryKey)->byTheme($themeId)->byPhone()->find();
            if ($communicationTheme) {
                $eventCode = $communicationTheme->phone_dialog_number;
                if ($eventCode == '' || $eventCode == 'AUTO') {

                    // выдаем автоответчик
                    $data = array();
                    $data[] = self::combineReplicaToHero(array('ch_from' => "$characterId"));

                    $character = Characters::model()->byId($characterId)->find();

                    if ($character) {
                        $data[0]['title'] = $character->title;
                        $data[0]['name'] = $character->fio;
                    }
                    PhoneService::registerOutgoing($simulation->id, $characterId, $time);
                    $result = array(
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
                    $replica = Dialog::model()->getFirstReplica($eventCode);
                    if (false == FlagsService::isAllowToStartDialog($simulation, $eventCode)) {
                        // событие не проходит по флагам -  не пускаем его
                        return array(
                            'result' => 1, 
                            'events' => array()
                        );
                    }


                    $data = EventService::getReplicaByCode($eventCode, $simulation->id);

                    $result = array(
                        'result' => 1,
                        'events' => array(
                            array(
                                'result' => 1,
                                'data' => $data,
                                'eventType' => 1   
                            )
                         )
                    );
                }
            }
        }
        // WTF? This does not even work
        if (null === $result) {

            // регистрируем исходящий вызов
            PhoneService::registerOutgoing($simulation->id, $characterId, $time);

            // подготовим список тем
            $themes = PhoneService::getThemes($characterId);
            $data = array();
            foreach($themes as $themeId => $themeName) {
                $data[] = self::combineReplicaToHero(array('id' => $themeId, 'ch_from' => "{$characterId}", 'text' => $themeName));
            }
            
            $result = array(
                'result' => 1,
                'data'   => $data,
            );
        }
        
        return $result;
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
}


