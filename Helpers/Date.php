<?php

namespace Skvn\Base\Helpers;

class Date
{
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

}