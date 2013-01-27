<?php



/**
 * Description of DateHelper
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DateHelper {
    
    /**
     *
     * @param int $date
     * @return string
     */
    public static function toString($date) {
        return date('d.m.Y G:i', $date);
    }
    
    public static function dateStringToTimestamp($date) {
        if (!strstr($date, '.')) return false;
        
        $data = explode('.', $date);
        $day = $data[0];
        $mon = $data[1];
        $year = $data[2];
        return gmmktime(0, 0, 0, $mon, $day, $year);
    }
    
    public static function timestampTimeToArr($unixtimeMins) {
        $clockH = floor($unixtimeMins/60);
        $clockM = $unixtimeMins-($clockH*60);
        return array('h' => $clockH, 'm' => $clockM);
    }
    
    public static function timestampTimeToString($unixtimeMins) {
        $data = self::timestampTimeToArr($unixtimeMins);
        return $data['h'].':'.$data['m'];
    }
    
    public static function timestampFullTimeToArray($variance) {
        $unixtimeMins = floor($variance/60);
        $clockH = floor($unixtimeMins/60);
        $clockM = $unixtimeMins-($clockH*60);
        $clockS = $variance-($unixtimeMins*60);
        
        return array(
            'h' => $clockH,
            'm' => $clockM,
            's' => $clockS
        );
    }
    
    public static function timestampFullTimeToString($variance) {
        $data = self::timestampFullTimeToArray($variance);
        return $data['h'].':'.$data['m'].':'.$data['s'];
    }


    /**
     * Преобразование времени в timestamp
     * @param string $time 12:24
     * @return false;
     */
    public static function timeToTimestamp($time) {
        if (!strstr($time, ':')) return false;
            
        $eventTimeData = explode(':', $time);
        if (isset($eventTimeData[1])) {
            return $eventTimeData[0] * 60 + $eventTimeData[1];
        }
        return false;    
    }
    
    /**
     *
     * @return int
     */
    public static function getCurrentTimestampDate() {
        return gmmktime(0, 0, 0, date('m'), date('d'), date('Y'));
        //return time();
        //return mktime(0, 0, 0, date('m'), date('d'), date('Y'));
    }
}


