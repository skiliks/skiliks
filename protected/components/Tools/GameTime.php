<?php

class GameTime
{
    const set_default_timezone = false;

    const today = '04.10.2012';

    const time_zone = "Europe/Moscow";

    /**
     * @return string
     */
    private static function getTimeZone()
    {
        return self::set_default_timezone ? date_default_timezone_get() : self::time_zone;
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

        $date = new DateTime(self::today . ' ' . $time, new DateTimeZone(self::getTimeZone()));

        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @param $time
     * @return mixed
     */
    public static function timeToSeconds($time)
    {

        $el = explode(':', $time);
        $el[2] = isset($el[2]) ? $el[2] : 0;
        return ($el[0] * 60 * 60) + ($el[1] * 60) + $el[2];
    }

    /**
     * @param $time
     * @return string
     * @throws CException
     */
    public static function getTime($time)
    {
        $el = explode(':', $time);
        if (count($el) < 2) {
            throw new CException("Неверный формат времени, нужно HH:MM:SS");
        } elseif (count($el) == 2) {
            throw new CException("Формат не нуждается в преобразовании! с HH:MM:SS в HH:MM");
        }
        return $el[0] . ':' . $el[1];
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
