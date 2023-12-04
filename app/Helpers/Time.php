<?php 

namespace App\Helpers;

use DateTime;
use DateTimeZone;
use Carbon\Carbon;
use DateInterval;

class Time {
    public static function getToday()
    {
        $year = now()->year;
        $month = now()->month<10?'0'.now()->month:now()->month;
        $day = now()->day<10?'0'.now()->day:now()->day;

        return "{$year}-{$month}-{$day}";
    }

    public static function getTimestamp()
    {
        $Date = new DateTime();
        return $Date->setTimezone(new DateTimeZone('Asia/Jakarta'))->format("Y-m-d H:i:s");
    }

    public static function changeTimeZone($time)
    {
        $Date = new DateTime($time);
        return $Date->setTimezone(new DateTimeZone('Asia/Jakarta'))->format("Y-m-d H:i:s");
    }

    public static function calculateWorkingHours($start)
    {
        $start = new Carbon($start);
        return $start->diff(new Carbon(self::getTimestamp()))->format('%H:%I');
    }

    public static function getDateFromTimestamp($timestamp)
    {
        $date = new Carbon($timestamp);
        return $date->format('d');
    }
    public static function getMonthFromTimestamp($timestamp)
    {
        $date = new Carbon($timestamp);
        return $date->format('m');
    }
    public static function getYearFromTimestamp($timestamp)
    {
        $date = new Carbon($timestamp);
        return $date->format('Y');
    }

    public static function getHourMinuteFromTimestamp($timestamp)
    {
        $date = new Carbon($timestamp);
        return $date->format('H:i');
    }

    public static function getDifferenceBetweenTwoDates($start,$end)
    {
        $start = new DateTime($start);
        return $start->diff(new DateTime($end))->format('%a');
    }

    public static function addDays($curr,$days)
    {
        $date = new Carbon($curr);
        return $date->addDays($days);
    }
}