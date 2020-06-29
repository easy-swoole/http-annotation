<?php


namespace EasySwoole\HttpAnnotation\Annotation\AbstractInterface;


use EasySwoole\Annotation\Annotation;
use EasySwoole\HttpAnnotation\Annotation\ObjectAnnotation;

interface ParserInterface
{
    function cache():CacheInterface;

    public function getAnnotationParser(): Annotation;

    function scanAnnotation(string $pathOrClass,bool $cache = true): array;

    function getObjectAnnotation(string $class, ?int $filterType = null): ObjectAnnotation;
}