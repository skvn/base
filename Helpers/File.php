<?php

namespace Skvn\Base\Helpers;

use Skvn\Base\Exceptions\InvalidArgumentException;

class File
{

    public static function ls($dir){
        return self :: findFiles($dir, ['recursive' => false]);
    }

    public static function findFiles($dir, $options = [])
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException("The dir argument must be a directory: $dir");
        }
        $dir = rtrim($dir, DIRECTORY_SEPARATOR);
        $list = [];
        $handle = opendir($dir);
        if ($handle === false) {
            throw new InvalidArgumentException("Unable to open directory: $dir");
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                $list[] = $path;
            } elseif ($options['recursive'] ?? true) {
                $list = array_merge($list, static::findFiles($path, $options));
            }
        }
        closedir($handle);

        return $list;
    }


}