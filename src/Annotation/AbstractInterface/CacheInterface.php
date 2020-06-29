<?php


namespace EasySwoole\HttpAnnotation\Annotation\AbstractInterface;


interface CacheInterface
{
    function set(string $class,$data);

    function get(string $class);

    function delete(string $class);

    function flush();
}