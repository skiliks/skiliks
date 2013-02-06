<?php


/**
 * Сервис по работе с телефоном
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class PhoneService {

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
    public function setHistory( $simId, $time, $from_character, $to_character, $dialog_subtype, $step_number, $replica_number ) {
        
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
                $phoneCalls->insert();
                return true;
                
            } else {
                
                return false;
                
            }
        
            
    }

    /**
     * Получить список тем для телефона.
     * @param int $id
     * @return array
     */
    public static function getThemes($id) {

        $themes = CommunicationTheme::model()->byCharacter($id)->byPhone()->findAll();
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
        $model->call_type   = 1;
        $model->from_id     = 1;
        $model->to_id       = $characterId; // какому персонажу мы звоним
        $model->insert();
    }
    
    public static function registerMissed($simId, $dialogId, $time) {
        
        $dialog = Dialogs::model()->byId($dialogId)->find();
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
                'type' => $item->call_type
            );
        }
        
        return $list;
    }
    
    public static function call($simulation, $themeId, $characterId, $time)
    {
        $result = null;
        
        // нам передана тема
        if ($themeId > 0) {
            $mailThemeModel = CommunicationTheme::model()->byCharacter($characterId)->byTheme($themeId)->byPhone()->find();
            if ($mailThemeModel) {
                $eventCode = $mailThemeModel->phone_dialog_number;
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
                    $eventRunResult = EventService::allowToRun($eventCode, $simulation->id, 1, 0);
                    if ($eventRunResult['compareResult'] === false || $eventRunResult===false) {
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


