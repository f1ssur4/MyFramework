<?php
namespace myf\lib;

class Cache
{
    public function get($name)
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/app/tmp/cache/' . md5($name) . '.txt';
        if (file_exists($file)){
            $content = unserialize(file_get_contents($file));
            if ($content['end_time'] >= time()){
                return $content['data'];
            }
            unlink($file);
        }
        return false;
    }

    public function set($name, $data, $timeToLive = 3600)
    {
        $content['data'] = $data;
        $content['end_time'] = time() + $timeToLive;
        $file = $_SERVER['DOCUMENT_ROOT'] . '/app/tmp/cache/' . md5($name) . '.txt';
        if (file_put_contents($file, serialize($content))){
            return true;
        }
        return false;
    }

    public function delete($name)
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . '/app/tmp/cache/' . md5($name) . '.txt';
        if (file_exists($file)){
            unlink($file);
        }
    }
}