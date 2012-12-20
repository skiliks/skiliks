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
}

