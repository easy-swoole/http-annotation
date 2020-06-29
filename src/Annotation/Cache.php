<?php


namespace EasySwoole\HttpAnnotation\Annotation;


use EasySwoole\Component\Singleton;
use EasySwoole\HttpAnnotation\Annotation\AbstractInterface\CacheInterface;

class Cache implements CacheInterface
{
    private $data = [];

    use Singleton;

    function set(string $class,$data)
    {
        $this->data[md5($class)] =$data;
    }

    function get(string $class)
    {
        $key = md5($class);
        if(isset($this->data[$key])){
            return $this->data[$key];
        }
        return null;
    }


    function delete(string $class)
    {
        unset($this->data[md5($class)]);
    }

    function flush()
    {
        $this->data = [];
    }
}