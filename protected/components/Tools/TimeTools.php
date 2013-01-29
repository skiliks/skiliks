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
    
    public static function minutesToTime($minutes)
    {
        return sprintf('%02s:%02s:00', floor($minutes/60), $minutes%60);
    }
}

