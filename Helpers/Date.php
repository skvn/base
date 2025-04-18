<?php

namespace Skvn\Base\Helpers;

class Date
{
    const WEEKEND_IMPACT_NONE = 0;
    const WEEKEND_IMPACT_SUNDAY = 1;
    const WEEKEND_IMPACT_FULL = 2;
    const WEEKEND_IMPACT_FULL_MONDAY = 3;
    const WEEKEND_IMPACT_MONDAY = 4;
    const WEEKEND_IMPACT_SUNDAY_MONDAY = 5;

    public static $monthNames = [
        1 => ['i' => 'январь', 'r' => 'января'],
        2 => ['i' => 'февраль', 'r' => 'февраля'],
        3 => ['i' => 'март', 'r' => 'марта'],
        4 => ['i' => 'апрель', 'r' => 'апреля'],
        5 => ['i' => 'май', 'r' => 'мая'],
        6 => ['i' => 'июнь', 'r' => 'июня'],
        7 => ['i' => 'июль', 'r' => 'июля'],
        8 => ['i' => 'август', 'r' => 'августа'],
        9 => ['i' => 'сентябрь', 'r' => 'сентября'],
        10 => ['i' => 'октябрь', 'r' => 'октября'],
        11 => ['i' => 'ноябрь', 'r' => 'ноября'],
        12 => ['i' => 'декабрь', 'r' => 'декабря'],
    ];

    public static $weekdayNames = [
        1 => ['short' => 'пн', 'full' => 'Понедельник'],
        2 => ['short' => 'вт', 'full' => 'Вторник'],
        3 => ['short' => 'ср', 'full' => 'Среда'],
        4 => ['short' => 'чт', 'full' => 'Четверг'],
        5 => ['short' => 'пт', 'full' => 'Пятница'],
        6 => ['short' => 'сб', 'full' => 'Суббота'],
        7 => ['short' => 'вс', 'full' => 'Воскресенье'],
    ];

    public static function format($ts = 0, $dateFormat = 'dd mm yyyy', $txtMonth = true, $txtToday = false, $smartYear = false)
    {
        if (empty($ts)) {
            $ts = time();
        }
        if (!is_numeric($ts)) {
            $ts = strtotime($ts);
        }
        if (!$ts) {
            return false;
        }
        switch ($dateFormat) {
            case 'simple':
                $txtMonth = true;
                $txtToday = false;
                $dateFormat = 'dd mm';
            break;
            case 'list':
                $txtMonth = false;
                $txtToday = false;
                $dateFormat = 'dd.mm.yyyy';
                $smartYear = 1;
            break;
            case 'full':
                $txtMonth = true;
                $txtToday = false;
                $dateFormat = 'dd mm yyyy';
            break;
        }

        $time = date('H:i:s', $ts);
        $year = date('Y', $ts);
        $month = date('m', $ts);
        $day = date('d', $ts);
        $hour = date('H', $ts);
        $min = date('i', $ts);
        $second = date('s', $ts);

        if ($txtToday) {
            if (date('Ymd') == $year . $month . $day) {
                $day = 'сегодня';
                $month = '';
                $year = '';
            } elseif (date('Ymd', strtotime('yesterday')) == $year . $month . $day) {
                $day = 'вчера';
                $month = '';
                $year = '';
            } elseif (date('Ymd', strtotime('tomorrow')) == $year . $month . $day) {
                $day = 'завтра';
                $month = '';
                $year = '';
            }
        }
        if ($month != '') {
            $day = intval($day);
        }
        if ($txtMonth && $month != '') {
            $month = static::$monthNames[intval($month)]['r'] ?? '';
            $monthI = static::$monthNames[intval($month)]['i'] ?? '';
        }
        if ($month != '') {
            if (!substr_count($dateFormat, 'mm')) {
                $month = intval($month);
            }
        }
        $numDigY = substr_count($dateFormat, 'y');

        $returnDate = $dateFormat;
        if ($smartYear && $year == date('Y')) {
            $year = '';
            $returnDate = preg_replace('/\.?(y)+/', '', $returnDate);
        }
        $year = substr($year, -$numDigY, 4);
        $returnDate = preg_replace('/(y)+/', $year, $returnDate);
        $returnDate = preg_replace('/dd|d/', $day, $returnDate);
        $returnDate = preg_replace('/mm|m/', $month, $returnDate);
        $returnDate = preg_replace('/FF/', $month, $returnDate);

        $returnDate = str_replace('H', $hour, $returnDate);
        $returnDate = str_replace('M', $min, $returnDate);

        return $returnDate;
    }


    public static function addWorkDays($cur, $offset, $wi = self::WEEKEND_IMPACT_FULL)
    {
        if (!is_numeric($cur)) {
            $cur = strtotime($cur);
        }
        $offset = intval($offset);
        $sign = $offset > 0 ? '+' : '-';
        $offset = abs($offset);
        $ts = $cur;
        while ($offset > 0) {
            $ts = strtotime($sign . '1 day', $ts);
            if (!static::isDayOff($ts, $wi)) {
                $offset--;
            }
        }
        return $ts;
    }

    public static function isDayOff($ts, $wi = self::WEEKEND_IMPACT_FULL)
    {
        $wd = date('N', $ts);
        switch ($wi) {
            case self::WEEKEND_IMPACT_FULL;
                return in_array($wd, ['6', '7']);
            case self::WEEKEND_IMPACT_SUNDAY;
                return in_array($wd, ['7']);
            case self::WEEKEND_IMPACT_FULL_MONDAY;
                return in_array($wd, ['1', '6', '7']);
            case self::WEEKEND_IMPACT_MONDAY;
                return in_array($wd, ['1']);
            case self::WEEKEND_IMPACT_SUNDAY_MONDAY;
                return in_array($wd, ['1', '7']);
        }
        return false;
    }

    public static function listYearMonths($from, $to, $reverse = false)
    {
        $list = [];
        $months = 0;
        for ($i = date('Y', $from); $i <= date('Y', $to); $i++) {
            for ($j = 1; $j <= 12; $j++) {
                if ($i == date('Y', $from) && $j < date('m', $from)) {
                    continue;
                }
                if ($i == date('Y', $to) && $j > date('m', $to)) {
                    continue;
                }
                $ts = strtotime('+ ' . $months . ' months', $from);
                $list[date('Y-m', $ts)] = date('Y', $ts) . "' " . mb_convert_case(static::$monthNames[date('n', $ts)]['i'], MB_CASE_TITLE, 'UTF-8');
                $months++;
            }
        }
        if ($reverse) {
            $list = array_reverse($list);
        }
        return $list;
    }

    public static function listMonths()
    {
        $list = [];
        for ($i = 1; $i <= 12; $i++) {
            $list[$i] = mb_convert_case(static::$monthNames[$i]['i'], MB_CASE_TITLE, 'UTF-8');
        }
        return $list;
    }

    public static function listYears($from, $to, $reverse = true)
    {
        $first = date('Y', $from);
        $last = date('Y', $to);
        $list = [];
        for ($i = $first; $i <= $last; $i++) {
            $list[$i] = $i;
        }
        if ($reverse) {
            $list = array_reverse($list, true);
        }
        return $list;
    }

    public static function listDays()
    {
        $list = [];
        for ($i = 1; $i <= 31; $i++) {
            $list[$i] = $i;
        }
        return $list;
    }

    public static function weekdayName($ts, $form = 'short', $default = null)
    {
        if (!is_numeric($ts)) {
            $ts = strtotime($ts ?? 'now');
        }
        $idx = $ts < 8 ? $ts : date('N', $ts);
        return static::$weekdayNames[$idx][$form] ?? $default;
    }

    public static function monthName($ts, $form = 'i', $default = null)
    {
        if (!is_numeric($ts)) {
            $ts = strtotime($ts);
        }
        $idx = $ts < 13 ? $ts : date('n', $ts);
        return static::$monthNames[$idx][$form] ?? $default;
    }

}