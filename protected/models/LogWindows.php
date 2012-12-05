<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 05.12.12
 * Time: 21:52
 * To change this template use File | Settings | File Templates.
 */
class LogWindows extends CActiveRecord {

    /**
     *
     * @param type $className
     * @return Characters
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'log_windows';
    }
}
