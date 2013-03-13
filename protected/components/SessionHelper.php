<?php



/**
 *  Анхелпер по работе с сессиями.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
final class SessionHelper
{

    /**
     *
     * @var string
     */
    private static $_sid;

    public static function setSid($sid)
    {
        self::$_sid = $sid;
    }

    public static function getSid()
    {
        return self::$_sid;
    }

    /**
     * Получить uid по sid.
     * @param string $sid
     * @return int
     * @deprecated
     */
    public static function getUidBySid()
    {
        $sid = Yii::app()->request->getParam('sid');
        assert(strlen($sid) > 0);
        session_id($sid);

        if (isset(Yii::app()->session['uid'])) {
            return Yii::app()->session['uid'];
        } else {
           return null;
           // throw new Exception('Не могу найти такого пользователя');
        }
    }

    /**
     * Получение модели пользователя по sid
     * @throws Exception
     * @internal param int $sid
     * @return Users
     */
    public static function getUserBySid()
    {
        $user_id = Yii::app()->session['uid'];
        if (!$user_id) throw new Exception('cant find user');
        $user = YumUser::model()->findByPk($user_id);
        if (!$user) throw new Exception('cant find user');

        return $user;
    }

    public static function getSimIdBySid($sid)
    {
        // stupidity, TODO: make normal sessions
        assert(strlen($sid) > 0);
        session_id($sid);
        $simulation = Simulation::model()->findByPk(Yii::app()->session['simulation']);
        if (!$simulation) throw new CException(sprintf("Не могу получить симуляцию по ID %d", Yii::app()->session['simulation']));

        return $simulation->primaryKey;
    }

    public static function isAuth(){
        $user_id = Yii::app()->session['uid'];
        $user = YumUser::model()->findByPk($user_id);
        if($user !== null){
            return true;
        } else {
            return false;
        }
    }
}


