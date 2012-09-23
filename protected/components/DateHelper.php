<?php



/**
 * Description of DateHelper
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DateHelper {
    
    public static function toString($date) {
        return date('d.m.Y G:i', $date);
    }
    
    public static function timestampTimeToArr($unixtimeMins) {
        $clockH = floor($unixtimeMins/60);
        $clockM = $unixtimeMins-($clockH*60);
        return array('h' => $clockH, 'm' => $clockM);
    }
}

?>
