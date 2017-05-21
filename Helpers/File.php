<?php

namespace Skvn\Base\Helpers;

use Skvn\Base\Exceptions\InvalidArgumentException;
use Skvn\Base\Exceptions\FilesystemException;

class File
{

    public static function mkdir($dir, $mode = 0755, $recursive = true)
    {
        if (!file_exists($dir)) {
            return mkdir($dir, $mode, $recursive);
        }
        return true;
    }


    public static function ls($dir, $opts = []){
        $options = ['recursive' => false];
        if (empty($opts['paths'])) {
            $options['basename'] = true;
        }
        return self :: findFiles($dir, $options);
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
                $list[] = !empty($options['basename']) ? basename($path) : $path;
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

    static function normalizeFilename($filename)
    {
        $filename = preg_replace("#\/{2,}#", "/", $filename);
        $filename = preg_replace("#\/+$#", "", $filename);
        return $filename;
    }

    static function cp($src, $dest)
    {
        if (!is_dir($src)) {
            if (!is_dir($dest)) {
                self :: mkdir(dirname($dest));
            } else {
                $dest = $dest . '/' . basename($src);
            }
            if (@copy($src, $dest) === false) {
                throw new FilesystemException('failed to copy file', array('src' => $src, 'dest' => $dest));
            }
            return;
        }
        self :: mkdir($dest);
        $items = self :: ls($src);

        $total_items = $items;
        while (count($items) > 0) {
            $current_items = $items;
            $items = [];
            foreach ($current_items as $item) {
                $full_path = $src . '/' . $item;
                if (is_file($full_path)) {
                    copy($full_path, $dest . '/' . $item);
                } elseif (is_dir($full_path)) {
                    self :: mkdir($dest . '/' . $item);
                    $new_items = self :: ls($full_path);
                    $items = array_merge($items, $new_items);
                    $total_items = array_merge($total_items, $new_items);
                    unset($new_items);
                }
            }
        }
        if ($total_items) {
            clearstatcache();
        }

        return $total_items;
    }




}