<?php

class GameTime
{
    const SET_DEFAULT_TIMEZONE = false;

    const TODAY = '04.10.2012';

    const TIME_ZONE = "Europe/Moscow";

    /**
     * @return string
     */
    private static function getTimeZone()
    {
        return self::SET_DEFAULT_TIMEZONE ? date_default_timezone_get() : self::TIME_ZONE;
    }

    /**
     * @param $datetime
     * @return string
     */
    public static function getDateTime($datetime)
    {
        $date = new DateTime($datetime, new DateTimeZone(self::getTimeZone()));

        return $date->format('d.m.Y H:i');
    }

    /**
     * @param $time
     * @return string
     */
    public static function setTimeToday($time)
    {
        $date = new DateTime(self::TODAY . ' ' . $time, new DateTimeZone(self::getTimeZone()));

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $time
     * @return mixed
     */
    public static function timeToSeconds($time)
    {
        $elements = explode(':', $time);
        $elements[2] = isset($elements[2]) ? $elements[2] : 0;
        return ($elements[0] * 60 * 60) + ($elements[1] * 60) + $elements[2];
    }

    /**
     * @param $time
     * @return string
     * @throws CException
     */
    public static function getTime($time)
    {
        $elements = explode(':', $time);
        if (count($elements) < 2) {
            throw new CException("Неверный формат времени, нужно HH:MM:SS");
        } elseif (count($elements) == 2) {
            throw new CException("Формат не нуждается в преобразовании! с HH:MM:SS в HH:MM");
        }

        return $elements[0] . ':' . $elements[1];
    }

    /**
     * @param $datetime
     * @return int
     */
    public static function getUnixDateTime($datetime)
    {
        return strtotime($datetime);
    }

    /**
     * @return string
     */
    public static function setNowDateTime()
    {
        $date = new DateTime('now', new DateTimeZone(self::getTimeZone()));

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $datetime
     * @return string
     */
    public static function setUnixDateTime($datetime)
    {
        $date = new DateTime();
        $date->setTimezone(new DateTimeZone(self::getTimeZone()));
        $date->setTimestamp($datetime);

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $time
     * @param $minutes
     * @return string
     */
    public static function addMinutesTime($time, $minutes)
    {
        $date = new DateTime($time, new DateTimeZone(self::getTimeZone()));
        $date->add(new DateInterval("PT" . $minutes . "M"));

        return $date->format("H:i:s");
    }
}
