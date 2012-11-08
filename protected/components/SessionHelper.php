<?php



/**
 * Хелпер по работе с сессиями.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
final class SessionHelper {
    
    /**
     *
     * @var string
     */
    private static $_sid;
    
    public static function setSid($sid) {
        self::$_sid = $sid;
    }
    
    public static function getSid() {
        return self::$_sid;
    }
    
    /**
     * Получить uid по sid.
     * @param string $sid
     * @return int
     */
    public static function getUidBySid() {
        session_id(Yii::app()->request->getParam('sid', false));
        if (isset(Yii::app()->session['uid']))
            return Yii::app()->session['uid'];
        return false;
    }
    
    /**
     * Получение модели пользователя по sid
     * @param int $sid
     * @return Users 
     */
    public static function getUserBySid() {
        $uid = self::getUidBySid();
        if (!$uid) throw new Exception('cant find user');
        
        $user = Users::model()->findByAttributes(array('id'=>$uid));
        if (!$user) throw new Exception('cant find user');
        
        return $user;
    }
    
    public static function getSimIdBySid($sid) {
        // получаем uid
        $uid = SessionHelper::getUidBySid();
        if (!$uid) throw new Exception("Не определить uid по sid : {$sid}");

        // получаем идентификатор симуляции
        $simId = SimulationService::get($uid);
        if (!$simId) throw new Exception("Не определить сумуляцию по uid : {$uid}");
        
        return $simId;
    }
}

?>
