<?php

namespace Skvn\Base\Helpers;

class Str
{
    protected static $snake = [];
    protected static $studly = [];
    protected static $camel = [];
    protected static $underscore = [];


    public static function pos($what, $where)
    {
        return strpos($where, $what);
    }

    public static function count($what, $where)
    {
        return substr_count($where, $what);
    }

    public static function contains($what, $where)
    {
        foreach ((array) $what as $pattern) {
            if ($pattern != '' && mb_strpos($where, $pattern) !== false) {
                return true;
            }
        }
        return false;
    }

    public static function startsWith($what, $where)
    {
        foreach ((array) $what as $pattern) {
            if ($pattern != '' && strpos($where, $pattern) === 0 ) {
                return true;
            }
        }

        return false;
    }

    public static function snake($value)
    {
        $key = $value;

        if (isset(static::$snake[$key])) {
            return static::$snake[$key];
        }

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);

            $value = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1-', $value));
        }

        return static::$snake[$key] = $value;
    }

    public static function underscore($value)
    {
        $key = $value;

        if (isset(static::$underscore[$key])) {
            return static::$underscore[$key];
        }

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);

            $value = strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1_', $value));
        }

        return static::$underscore[$key] = $value;
    }

    public static function camel($value)
    {
        if (isset(static::$camel[$value])) {
            return static::$camel[$value];
        }

        return static::$camel[$value] = lcfirst(static::studly($value));
    }


    public static function studly($value)
    {
        $key = $value;

        if (isset(static::$studly[$key])) {
            return static::$studly[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return static::$studly[$key] = str_replace(' ', '', $value);
    }

    public static function classBasename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }

    public static function xml2array($xml, $rootNode = null)
    {
        $arr = json_decode(json_encode(simplexml_load_string($xml)), true);
        return !empty($rootNode) ? ($arr[$rootNode] ?? []) : $arr;
    }

    public static function random($length)
    {
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }

    public static function uuid()
    {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
                       random_int(0, 65535),
                       random_int(0, 65535),
                       random_int(0, 65535),
                       random_int(16384, 20479),
                       random_int(32768, 49151),
                       random_int(0, 65535),
                       random_int(0, 65535),
                       random_int(0, 65535));
    }

    private static function chooseAmountWord($texts, $val, &$fem, $f)
    {
        $words = '';
        $fl = 0;
        if ($val >= 100) {
            $words .= $texts['hang'][intval($val / 100)];
            $val %= 100;
        }
        if ($val >= 20) {
            $words .= $texts['des'][intval($val / 10)];
            $val %= 10;
            $fl = 1;
        }
        switch ($val) {
            case 1:
                $fem = 1;
                break;
            case 2:
            case 3:
            case 4:
                $fem = 2;
                break;
            default:
                $fem = 3;
                break;
        }
        if ($val) {
            if ($val < 3 && $f > 0) {
                if ($f >= 2) {
                    $words .= $texts['_1_19'][$val];
                } else {
                    $words .= $texts['_1_2'][$val];
                }
            } else {
                $words .= $texts['_1_19'][$val];
            }
        }
        return $words;
    }


    public static function writtenRusAmount($amount)
    {
        $texts = [
            '_1_2' => [
                '1' => 'одна ',
                '2' => 'две '
            ],
            '_1_19' => [
                '1' => 'один ',
                '2' => 'два ',
                '3' => 'три ',
                '4' => 'четыре ',
                '5' => 'пять ',
                '6' => 'шесть ',
                '7' => 'семь ',
                '8' => 'восемь ',
                '9' => 'девять ',
                '10' => 'десять ',
                '11' => 'одиннацать ',
                '12' => 'двенадцать ',
                '13' => 'тринадцать ',
                '14' => 'четырнадцать ',
                '15' => 'пятнадцать ',
                '16' => 'шестнадцать ',
                '17' => 'семнадцать ',
                '18' => 'восемнадцать ',
                '19' => 'девятнадцать '
            ],
            'des' => [
                '2' => 'двадцать ',
                '3' => 'тридцать ',
                '4' => 'сорок ',
                '5' => 'пятьдесят ',
                '6' => 'шестьдесят ',
                '7' => 'семьдесят ',
                '8' => 'восемдесят ',
                '9' => 'девяносто '
            ],
            'hang' => [
                '1' => 'сто ',
                '2' => 'двести ',
                '3' => 'триста ',
                '4' => 'четыреста ',
                '5' => 'пятьсот ',
                '6' => 'шестьсот ',
                '7' => 'семьсот ',
                '8' => 'восемьсот ',
                '9' => 'девятьсот '
            ],
            'namerub' => [
                '1' => 'рубль ',
                '2' => 'рубля ',
                '3' => 'рублей '
            ],
            'nametho' => [
                '1' => 'тысяча ',
                '2' => 'тысячи ',
                '3' => 'тысяч '
            ],
            'namemil' => [
                '1' => 'миллион ',
                '2' => 'миллиона ',
                '3' => 'миллионов '
            ],
            'namemrd' => [
                '1' => 'миллиард ',
                '2' => 'миллиарда ',
                '3' => 'миллиардов '
            ],
            'kopeek' => [
                '1' => 'копейка ',
                '2' => 'копейки ',
                '3' => 'копеек '
            ]
        ];

        $s = ' ';
        $s1 = ' ';
        $s2 = ' ';
        $kop = intval(( intval($amount) * 100 - intval($amount) * 100));
        $amount = intval($amount);
        if ($amount >= 1000000000) {
            $many = 0;
            $s1 = static::chooseAmountWord($texts, intval($amount / 1000000000), $many, 3);
            $s .= $s1 . $texts['namemrd'][$many];
            $amount %= 1000000000;
        }
        if ($amount >= 1000000) {
            $many = 0;
            $s1 = static::chooseAmountWord($texts, intval($amount / 1000000), $many, 2);
            $s .= $s1 . $texts['namemil'][$many];
            $amount %= 1000000;
            if ($amount == 0) {
                $s .= 'рублей ';
            }
        }
        if ($amount >= 1000) {
            $many = 0;
            $s1 = static::chooseAmountWord($texts, intval($amount / 1000), $many, 1);
            $s .= $s1 . $texts['nametho'][$many];
            $amount %= 1000;
            if ($amount == 0) {
                $s .= 'рублей ';
            }
        }
        if ($amount != 0) {
            $many = 0;
            $s1 = static::chooseAmountWord($texts, $amount, $many, 0);
            $s .= $s1 . $texts['namerub'][$many];
        }
        if ($kop > 0) {
            $many = 0;
            static::chooseAmountWord($texts, $kop, $many, 1);
            $s .= $kop . ' ' . $texts['kopeek'][$many];
        } else {
            $s .= ' 00 копеек';
        }

        return $s;
    }

    public static function cachePrefix($hash, $args = [])
    {
        $level = $args['level'] ?? 2;
        $parts = [];
        for ($i=0; $i<$level; $i++) {
            $part = substr($hash, $i*2, 2);
            foreach ($args['replaces'] ?? [] as $from => $to) {
                $part = str_replace($from, $to, $part);
            }
            $parts[] = $part;
        }
        return implode('/', $parts);
    }

    public static function nform($num, $var1, $var2, $var3)
    {
        if ($num >= 10 && $num < 20) {
            return $var3;
        }
        $num = strval($num);
        $int = intval(substr($num, -1, strlen($num)));
        switch ($int) {
            case 1:
                return $var1;
            case 2:
            case 3:
            case 4:
                return $var2;
            default:
                return $var3;
        }
    }

    public static function parsePhpDoc($comment)
    {
        return [
            'summary' => static::parsePhpDocSummary($comment),
            'detail' => static::parsePhpDocDetail($comment),
            'tags' => static::parsePhpDocTags($comment)
        ];
    }

    public static function parsePhpDocTags($comment)
    {
        $comment = "@description \n" . strtr(trim(preg_replace('/^\s*\**( |\t)?/m', '', trim($comment, '/'))), "\r", '');
        $parts = preg_split('/^\s*@/m', $comment, -1, PREG_SPLIT_NO_EMPTY);
        $tags = [];
        foreach ($parts as $part) {
            if (preg_match('/^(\w+)(.*)/ms', trim($part), $matches)) {
                $name = $matches[1];
                if (!isset($tags[$name])) {
                    $tags[$name] = trim($matches[2]);
                } elseif (is_array($tags[$name])) {
                    $tags[$name][] = trim($matches[2]);
                } else {
                    $tags[$name] = [$tags[$name], trim($matches[2])];
                }
            }
        }
        return $tags;
    }

    public static function parsePhpDocSummary($comment)
    {
        $docLines = preg_split('~\R~u', $comment);
        if (isset($docLines[1])) {
            return trim($docLines[1], "\t *");
        }
        return '';
    }

    public static function parsePhpDocDetail($comment)
    {
        $comment = strtr(trim(preg_replace('/^\s*\**( |\t)?/m', '', trim($comment, '/'))), "\r", '');
        if (preg_match('/^\s*@\w+/m', $comment, $matches, PREG_OFFSET_CAPTURE)) {
            $comment = trim(substr($comment, 0, $matches[0][1]));
        }
        if ($comment !== '') {
            return rtrim($comment);
        }
        return '';
    }

    public static function transliterate($input, $url_escape = true, $tolower=false, $keepPunctuation = false)
    {
        $arrRus = ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м',
                        'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ь',
                        'ы', 'ъ', 'э', 'ю', 'я',
                        'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М',
                        'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ь',
                        'Ы', 'Ъ', 'Э', 'Ю', 'Я'];
        $arrEng = ['a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm',
                        'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'kh', 'cz', 'ch', 'sh', 'shh', '',
                        'y', '', 'e', 'yu', 'ya',
                        'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'Zh', 'Z', 'I', 'Y', 'K', 'L', 'M',
                        'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Kh', 'Cz', 'Ch', 'Sh', 'Shh', '',
                        'Y', '', 'E', 'Yu', 'Ya'];

        if (!$keepPunctuation) {
            $input = str_replace(' ', '-', $input);
        }
        if ($tolower) {
            $input = mb_strtolower($input, 'UTF-8');
        }
        $result = str_replace($arrRus, $arrEng, $input);

        if (!$keepPunctuation) {
            $result = preg_replace("#[^_-a-zA-Z0-9]#i", '', $result);
        }

        if ($url_escape) {
            $result = str_replace([' ', '/', '\\', ','], '-', $result);
            $result = urlencode($result);
        }

        return $result;
    }


}
