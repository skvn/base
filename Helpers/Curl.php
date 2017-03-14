<?php

namespace Skvn\Base\Helpers;


class Curl
{
    static function fetch($uri, $params = [])
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $uri);
        $defaults = [
            CURLOPT_TIMEOUT => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_DNS_CACHE_TIMEOUT => 5
        ];
        foreach ($params as $k => $v) {
            $defaults[$k] = $v;
        }
        foreach ($defaults as $k => $v) {
            curl_setopt($curl, $k, $v);
        }
        $t = microtime(true);
        $result = curl_exec($curl);
        $info = [
                    'uri' => $uri,
                    'code' => curl_getinfo($curl, CURLINFO_RESPONSE_CODE),
                    'error' => curl_error($curl),
                    'error_num' => curl_errno($curl),
                    'response' => $result,
                    'time' => round(microtime(true) - $t, 2)
        ];
        curl_close($curl);
        return $info;
    }



}