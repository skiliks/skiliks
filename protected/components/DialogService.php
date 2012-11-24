<?php



/**
 * Description of DialogService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogService {
    
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
            'ch_from'           => $dialog->ch_from,
            'ch_from_state'     => $dialog->ch_from_state,
            'ch_to'             => $dialog->ch_to,
            'ch_to_state'       => $dialog->ch_to_state,
            'dialog_subtype'    => $dialog->dialog_subtype,
            'text'              => $dialog->text,
            'sound'             => $dialog->sound,
            'duration'          => $dialog->duration,

            'step_number'       => $dialog->step_number,
            'replica_number'    => $dialog->replica_number,
            'next_event_code'   => $dialog->next_event_code,
            'code'              => $dialog->code
        );
    }
}

?>
