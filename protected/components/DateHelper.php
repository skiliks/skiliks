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
}

?>
