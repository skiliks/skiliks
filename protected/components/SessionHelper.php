<?php



/**
 * Хелпер по работе с сессиями.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SessionHelper {
    
    /**
     * Получить uid по sid.
     * @param string $sid
     * @return int
     */
    public static function getUidBySid($sid) {
        $session = UsersSessions::model()->findByAttributes(array('session_id'=>$sid));
        if ($session) return $session->user_id;
        return false;
    }
}

?>
