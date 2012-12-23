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
        session_id(Yii::app()->request->getParam('sid', null));
        
        if (isset(Yii::app()->session['uid'])) {
            return Yii::app()->session['uid'];
        }
        if (!$userId)
                throw new Exception('Не могу найти такого пользователя');
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
        // stupidity, TODO: make normal sessions
        session_id($sid);
        $simulation = Simulations::model()->findByPk(Yii::app()->session['simulation']);
        if (!$simulation) throw new CException(sprintf("Не могу получить симуляцию по ID %d", Yii::app()->session['simulation']));
        
        return $simulation->primaryKey;
    }
}


