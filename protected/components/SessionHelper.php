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
    
    /**
     * Получение модели пользователя по sid
     * @param int $sid
     * @return Users 
     */
    public static function getUserBySid($sid) {
        $uid = self::getUidBySid($sid);
        if (!$uid) throw new Exception('cant find user');
        
        $user = Users::model()->findByAttributes(array('id'=>$uid));
        if (!$user) throw new Exception('cant find user');
        
        return $user;
    }
}

?>
