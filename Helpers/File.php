<?php

namespace Skvn\Base\Helpers;

use Skvn\Base\Exceptions\InvalidArgumentException;
use Skvn\Base\Exceptions\FilesystemException;

class File
{

    public static function mkdir($dir, $mode = 0755, $recursive = true)
    {
        if (!is_dir($dir)) {
            mkdir($dir, $mode, $recursive);
        }
        return true;
    }

    public static function safeMkdir($dir, $mode = 0755, $recursive = true)
    {
        if (!file_exists($dir)) {
            $old = error_reporting(0);
            $res = mkdir($dir, $mode, $recursive);
            error_reporting($old);
            return $res;
        }
        return true;
    }

    static function safeExists($file)
    {
        return @fclose(@fopen($file, "r"));
    }

    public static function ls($dir, $opts = []){
        $options = ['recursive' => false];
        if (empty($opts['paths'])) {
            $options['basename'] = true;
        }
        if (isset($opts['folders'])) {
            $options['folders'] = $opts['folders'];
        }
        return self :: findFiles($dir, $options);
    }

    public static function rglob($path, $pattern)
    {
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false)
        {
            if (substr($file, 0, 1) == '.') continue;
            $rfile = "{$path}/{$file}";
            if (is_dir($rfile)) {
                foreach (self :: rglob($rfile, $pattern) as $ret) {
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
            } else {
                if (!empty($options['folders'])) {
                    $list[] = !empty($options['basename']) ? basename($path) : $path;
                }
                if ($options['recursive'] ?? true) {
                    $list = array_merge($list, static::findFiles($path, $options));
                }
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
        $items = self :: ls($src, ['folders' => true]);

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
                    $new_items = array_map(function($d) use ($item){
                        return $item . '/' . $d;
                    }, self :: ls($full_path, ['folders' => true]));
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

    static function getMimeType($filename)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filename);
        finfo_close($finfo);
        return $mime;
    }

    static function getExtension($filename, $default = "png")
    {
        $info = pathinfo($filename);
        return !empty($info['extension']) ? $info['extension'] : $default;
    }

    static function getHumanFilesize($bytes, $decimals = 2)
    {
        $sz = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $factor = intval(floor((strlen($bytes) - 1) / 3));
        $suffix = $sz[$factor] ?? '';
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . $suffix;
    }




}