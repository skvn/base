<?php

namespace Skvn\Base\Helpers;

use Skvn\Base\Exceptions\InvalidArgumentException;
use Skvn\Base\Exceptions\FilesystemException;

class File
{

    public static function ls($dir){
        return self :: findFiles($dir, ['recursive' => false]);
    }

    public static function walkDir($path, $pattern)
    {
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false)
        {
            if (substr($file, 0, 1) == '.') continue;
            $rfile = "{$path}/{$file}";
            if (is_dir($rfile)) {
                foreach (self :: walkDir($rfile, $pattern) as $ret) {
                    yield $ret;
                }
            } else {
                if (fnmatch($pattern, $file)) yield $rfile;
            }
        }
        closedir($dh);
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

    static function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            throw new InvalidArgumentException($dir . ' is not a directory');
        }
    }

    static function rm($file)
    {
        if (!file_exists($file)) {
            return false;
        }
        if (!is_dir($file)) {
            if (!@unlink($file)) {
                throw new FilesystemException('Failed to remove file ' . $file);
            }
            return;
        }
        if (!$handle = @opendir($file)) {
            throw new FilesystemException('Failed to open directory ' . $file);
        }
        while (($f = readdir($handle)) !== false) {
            if ($f == '.' || $f == '..')
                continue;
            static :: rm($file . '/' . $f);
        }
        closedir($handle);
        if (!@rmdir($file)) {
            throw new FilesystemException('Failed to remove directory ' . $file);
        }
        clearstatcache();
        return true;
    }




}