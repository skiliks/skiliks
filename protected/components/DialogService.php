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
     * @return object
     */
    public static function get($dialogId) {
        $dialog = Dialogs::model()->byId($dialogId)->find();
        if (!$dialog) throw new Exception('Не могу загрузить модель диалога', 7);
        return $dialog;    
    }
    
    public static function dialogToArray($dialog) {
        return array(
            'id' => $dialog->id,
            'ch_from' => $dialog->ch_from,
            'ch_from_state' => $dialog->ch_from_state,
            'ch_to' => $dialog->ch_to,
            'ch_to_state' => $dialog->ch_to_state,
            'dialog_subtype' => $dialog->dialog_subtype,
            'text' => $dialog->text,
            'sound' => $dialog->sound,
            'duration' => $dialog->duration
        );
        
        // duration, sound
    }
}

?>
