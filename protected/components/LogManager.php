<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivan
 * Date: 1/14/13
 * Time: 11:55 AM
 * To change this template use File | Settings | File Templates.
 */
class LogManager
{

    const ACTION_CLOSE = "0"; //Закрытие окна

    const ACTION_OPEN = "1"; //Открытие окна

    const ACTION_SWITCH = "2"; //Переход в рамках окна

    const ACTION_ACTIVATED = "activated"; //Активация окна

    const ACTION_DEACTIVATED = "deactivated"; //Деактивация окна

    const RETURN_DATA = 'json'; //Тип возвращаемого значения JSON

    const RETURN_CSV = 'csv'; //Тип возвращаемого значения CSV

    const LOGIN = false; //Писать лог в файл? true - да, false - нет

    public $bom = "0xEF 0xBB 0xBF";

    protected $codes_documents = array(40,41,42);

    protected $codes_mail = array(10,11,12,13,14);

    protected $screens = array(
        1 => 'main screen',
        3 => 'plan',
        10 => 'mail',
        20 => 'phone',
        30 => 'visitor',
        40 => 'documents'
    );

    const MAIL_MAIN = 'mail main';
    const MAIL_PREVIEW = 'mail preview';
    const MAIL_NEW = 'mail new';
    const MAIL_PLAN = 'mail plan';

    protected $subScreens = array(
        1 => 'main screen',
        3 => 'plan',
        11 => 'mail main',
        12 => 'mail preview',
        13 => 'mail new',
        14 => 'mail plan',
        21 => 'phone main',
        23 => 'phone talk',
        24 => 'phone call',
        31 => 'visitor entrance',
        32 => 'visitor talk',
        41 => 'documents main',
        42 => 'documents files'
    );

    protected $actions = array(
        0             => 'close',
        1             => 'open',
        2             => 'switch',
        'activated'   => 'activated',
        'deactivated' => 'deactivated',
    );


    public function setUniversalLog() {

    }

}
