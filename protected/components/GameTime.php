<?php

class GameTime
{
    const set_default_timezone = false;

    const today = '04.10.2012';

    const time_zone = "Europe/Moscow";

    private static function getTimeZone() {
        return self::set_default_timezone?date_default_timezone_get():self::time_zone;
    }

    public static function getDateTime($datetime) {

        $date = new DateTime($datetime, new DateTimeZone(self::getTimeZone()));

        return $date->format('d.m.Y H:i');
    }

    public static function setTimeToday($time) {

        $date = new DateTime(self::today.' '.$time, new DateTimeZone(self::getTimeZone()));

        return $date->format('Y-m-d H:i:s');
    }

    public static function timeToSeconds($time) {

        $el = explode(':', $time);
        $el[2] = isset($el[2])?$el[2]:0;
        return ($el[0]*60*60) + ($el[1]*60) + $el[2];
    }

    public static function getTime($time) {
        $el = explode(':', $time);
        if(count($el) < 2) {
            throw new CException("Не верный формат времени, нужно HH:MM:SS");
        } elseif(count($el) == 2) {
            throw new CException("Фармат не нуждаеться в преобразовании! с HH:MM:SS в HH:MM");
        }
        return $el[0].':'.$el[1];
    }

    public static function getUnixDateTime($datetime) {
        return strtotime($datetime);
    }

    public static function setNowDateTime() {
        $date = new DateTime('now', new DateTimeZone(self::getTimeZone()));
        return $date->format('Y-m-d H:i:s');
    }

    public static function setUnixDateTime($datetime) {
        $date = new DateTime($datetime, new DateTimeZone(self::getTimeZone()));
        return $date->format('Y-m-d H:i:s');
    }

    public static function addMinutesTime($time, $minutes) {
        $date = new DateTime($time, new DateTimeZone(self::getTimeZone()));
        $date->add(new DateInterval("PT".$minutes."M"));
        return $date->format("H:i:s");
    }
}
