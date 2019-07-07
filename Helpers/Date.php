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
        'нет месяца',
        'Январь',
        'Февраль',
        'Март',
        'Апрель',
        'Май',
        'Июнь',
        'Июль',
        'Август',
        'Сентябрь',
        'Октябрь',
        'Ноябрь',
        'Декабрь'
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
                $list[date('Y-m', $ts)] = date('Y', $ts) . "' " . static::$monthNames[date('n', $ts)];
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
            $list[$i] = static::$monthNames[$i];
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
        return static::$weekdayNames[date('N', $ts)][$form] ?? $default;
    }

}