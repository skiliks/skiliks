<?php

/**
 * @author slavka
 */
class TimeTools
{
    /**
     * Crazy, but legasy code
     * @param int || float $time
     *
     * @return integer
     */
    public static function roundTime($time)
    {
        return ceil($time / 30) * 30;
    }

    /**
     * @param $seconds
     * @return string
     */
    public static function secondsToTime($seconds)
    {
        $hours = floor($seconds/(60*60));
        $minutes = floor($seconds/60) - $hours*60;
        $seconds = $seconds - $hours*60*60 - $minutes*60;
        
        return sprintf('%02s:%02s:%02s', $hours, $minutes, $seconds);
    }

    /**
     * @param $time
     * @return mixed
     */
    public static function timeToSeconds($time)
    {
        list($hours, $minutes, $seconds) = explode(':', $time);
        return ($seconds*1 + $minutes*60 + $hours*60*60);
    }

    /**
     * @param $time
     * @param $seconds
     * @return string
     */
    public static function timeStringPlusSeconds($time, $seconds)
    {
        $t = self::TimeToSeconds($time);
        return self::secondsToTime($t + $seconds);
    }
}

